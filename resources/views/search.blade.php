<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Word</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h1 class="mb-4">English to Karen Dictionary (အဲကလံး - ကညီစှီၤ ကျိၥ်လံၥ်ခီယ့ၤ)</h1>
            </div>
            <div class="col-md-6">
            <form id="searchForm">
                    <div class="form-group">
                        <label for="q">English Word</label>
                        <input type="text" name="q" id="q" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>

                <div class="mt-4" id="searchResult"></div>
            </div>

        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Autocomplete Setup
            $('#q').autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: '/api/search',
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            q: request.term,
                            autocomplete: true
                        }),
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function (data) {
                            if (data.suggestions) {
                                response(data.suggestions);
                            }
                        }
                    });
                },
                minLength: 2
            });

            // Form Submission with AJAX
            $('#searchForm').on('submit', function (e) {
                e.preventDefault();
                
                let query = $('#q').val();
                
                $.ajax({
                    url: '/api/search',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        q: query
                    }),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        let resultHtml = '<h3>Search Result</h3>';
                        
                        if (data.error) {
                            resultHtml += `<div class="alert alert-danger">${data.error}</div>`;
                        } else {
                            resultHtml += `<p><strong>Word:</strong> ${data.english_word}</p>`;
                            resultHtml += '<ul>';
                            
                            data.translations.forEach(function (translation) {
                                resultHtml += `<li><strong>${translation.part_of_speech.part_of_speech}:</strong> ${translation.translation}</li>`;
                            });
                            
                            resultHtml += '</ul>';
                        }
                        
                        $('#searchResult').html(resultHtml);
                    },
                    error: function (xhr) {
                        $('#searchResult').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                    }
                });
            });
        });
    </script>
</body>
</html>
