<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;

class WordToJsonController extends Controller
{
    public function showUploadForm()
    {
        return view('upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:doc,docx|max:2048',
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();

        $phpWord = IOFactory::load($filePath);
        $questions = [];
        $currentQuestion = null;

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getElements')) {
                    foreach ($element->getElements() as $textElement) {
                        if (method_exists($textElement, 'getText')) {
                            $text = $textElement->getText();
                            $isBold = false;

                            if (method_exists($textElement, 'getFontStyle') && $textElement->getFontStyle() && $textElement->getFontStyle()->isBold()) {
                                $isBold = true;
                            }

                            if (preg_match('/[?…]$/', $text)) {
                                if ($currentQuestion) {
                                    $questions[] = $currentQuestion;
                                }
                                $currentQuestion = ['question' => $text, 'options' => []];
                            } else {
                                if ($currentQuestion) {
                                    $option = ['text' => $text, 'is_correct' => $isBold];
                                    $currentQuestion['options'][] = $option;
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($currentQuestion) {
            $questions[] = $currentQuestion;
        }

        $json = json_encode($questions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return response()->json($json);
    }
}
