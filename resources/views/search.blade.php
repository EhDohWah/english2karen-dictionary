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
    <style>
        .large-text {
            font-size: 1.25rem; /* Adjust size as needed */
        }
    </style>
</head>
<body>
    <!-- Nav-bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">English to Karen Dictionary</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Dictionary</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                </ul>
            </div>
        </nav>


    <!-- Dictionary Search -->
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
                <h3 class="pt-4">Search Result (တၢ်အဆၢ)</h3>
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
                        let resultHtml = '<br>';
                        
                        if (data.error) {
                            resultHtml += `<div class="alert alert-danger">${data.error}</div>`;
                        } else {
                            resultHtml += `<p class="large-text"><strong>Word:</strong> ${data.english_word}</p>`;
                            resultHtml += '<ul>';
                            
                            data.translations.forEach(function (translation) {
                                resultHtml += `<li class="large-text"><strong>${translation.part_of_speech.part_of_speech}:</strong> ${translation.translation}</li>`;
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
