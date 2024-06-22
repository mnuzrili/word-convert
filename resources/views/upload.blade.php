<!DOCTYPE html>
<html>

<head>
    <title>Soal Convert</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    <link href="{{ asset('css/iziToast.css') }}" rel="stylesheet">
</head>

<body>
    @include('vendor.lara-izitoast.toast')
    <div class="flex v-screen mt-12">
        <div class="m-auto">
            <div class="card w-[800px] bg-neutral text-neutral-content">
                <div class="card-body items-center text-center">
                    <h2 class="card-title">Input Soal</h2>
                    <div class="card-actions justify-end">
                        <form id="uploadForm" enctype="multipart/form-data">
                            <input type="file" name="file" id="file"
                                class="file-input file-input-bordered file-input-primary w-full max-w-xs" required>
                            <button type="submit" class="btn btn-outline btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="result-card" class="flex v-screen m-4 invisible">
        <div class="m-auto">
            <div class="card w-auto bg-neutral text-neutral-content mt-4">
                <div class="card-body items-center text-center">
                    <h2 class="card-title">Result</h2>
                    <p>
                    <div id="result" class="text-left">
                    </div>
                    </p>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/iziToast.js') }}"></script>
    <script>
        const copyToCC = async (text) => {
            // let text = document.getElementById('myText').innerHTML;

            try {
                await navigator.clipboard.writeText(text);
                iziToast.success({
                    title: 'Success',
                    message: 'Berhasil di copy sayang...',
                    position: 'topCenter',

                });
            } catch (err) {
                console.error('Failed to copy: ', err);
            }
        }

        $(document).ready(function() {
            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                $.ajax({
                    url: '/convert',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        $('#result-card').removeClass('invisible');
                        $('#result').empty();
                        response = JSON.parse(data);
                        // console.log(response)
                        $('#result').append(`<ol class="list-decimal"></ol>`);
                        $.each(response, function(i, item) {
                            // console.log(item.question);
                            $('#result ol').append(
                                `<li><button onClick="copyToCC('${item.question}')" class="btn m-2 mt-8 text-left"> ${item.question}</button></li> <ul class="list-[lower-alpha] ml-4 ul-${i}"></ul>`
                            );

                            $.each(response[i].options, function(x, val) {
                                // console.log(val.text)
                                if (val.is_correct === true) {
                                    $(`#result ol .ul-${i}`).append(
                                        `<li><button onClick="copyToCC('${val.text}')" class="btn m-2 text-green-400"> ${val.text}</button></li>`
                                    );
                                } else {
                                    $(`#result ol .ul-${i}`).append(
                                        `<li><button onClick="copyToCC('${val.text}')" class="btn m-2"> ${val.text}</button></li>`
                                    );
                                }
                            });
                        });


                        // $('#result').html(JSON.stringify(data, null, 4));
                    },
                    error: function(xhr, status, error) {
                        $('#result').html('An error occurred: ' + error);
                    }
                });
            });
        });
    </script>
</body>

</html>
