<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Word</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="mb-4">Add New Word</h1>
                <form id="wordForm">
                    @csrf
                    <div class="form-group">
                        <label for="english_word">English Word</label>
                        <input type="text" name="english_word" id="english_word" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="part_of_speech">Part of Speech</label>
                        <select name="part_of_speech_id" id="part_of_speech" class="form-control" required>
                            @foreach ($partsOfSpeech as $partOfSpeech)
                                <option value="{{ $partOfSpeech->id }}">{{ $partOfSpeech->part_of_speech }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="translation">Translation</label>
                        <textarea name="translation" id="translation" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Word</button>
                </form>

                <h2 class="mt-5">Word List</h2>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>English Word</th>
                                <th>Translations</th>
                            </tr>
                        </thead>
                        <tbody id="wordListBody">
                            <!-- Word list will be populated here -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Controls -->
                <div class="mt-4" id="paginationControls">
                    <!-- Pagination controls will be populated here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Function to fetch and display words
            function fetchWords(page = 1) {
                $.ajax({
                    url: `/api/words?page=${page}`,
                    type: 'GET',
                    success: function (data) {
                        let resultHtml = '';
                        let paginationHtml = '';

                        if (data.data.length > 0) {
                            data.data.forEach(word => {
                                resultHtml += `<tr>
                                    <td>${word.english_word}</td>
                                    <td>
                                        <ul class="list-unstyled">`;
                                word.translations.forEach(translation => {
                                    resultHtml += `<li><strong>${translation.part_of_speech.part_of_speech}:</strong> ${translation.translation}</li>`;
                                });
                                resultHtml += `</ul></td></tr>`;
                            });

                            $('#wordListBody').html(resultHtml);

                            // Generate pagination controls
                            const { current_page, last_page } = data;
                            paginationHtml = `
                                <nav>
                                    <ul class="pagination">
                                        ${current_page > 1 ? `<li class="page-item"><a class="page-link" href="#" data-page="${current_page - 1}">Previous</a></li>` : ''}
                                        ${Array.from({ length: last_page }, (_, i) => i + 1).map(pageNumber => `
                                            <li class="page-item ${current_page === pageNumber ? 'active' : ''}">
                                                <a class="page-link" href="#" data-page="${pageNumber}">${pageNumber}</a>
                                            </li>`).join('')}
                                        ${current_page < last_page ? `<li class="page-item"><a class="page-link" href="#" data-page="${current_page + 1}">Next</a></li>` : ''}
                                    </ul>
                                </nav>`;
                        } else {
                            $('#wordListBody').html('<tr><td colspan="2" class="text-center">No words available</td></tr>');
                        }

                        $('#paginationControls').html(paginationHtml);
                    },
                    error: function (xhr) {
                        $('#wordListBody').html('<tr><td colspan="2" class="text-center">Error retrieving data</td></tr>');
                    }
                });
            }

            // Fetch words on page load
            fetchWords();

            // Handle form submission
            $('#wordForm').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: '/api/words',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        english_word: $('#english_word').val(),
                        part_of_speech_id: $('#part_of_speech').val(),
                        translation: $('#translation').val()
                    }),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        alert(data.message);
                        $('#wordForm')[0].reset();
                        fetchWords(); // Refresh the word list
                    },
                    error: function (xhr) {
                        alert('Error adding word');
                    }
                });
            });

            // Handle pagination click
            $('#paginationControls').on('click', '.page-link', function (e) {
                e.preventDefault();
                let page = $(this).data('page');
                fetchWords(page);
            });
        });
    </script>
</body>
</html>
