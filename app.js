document.addEventListener('DOMContentLoaded', () => {

   
    fetch('subjects.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Could not load subjects.');
            }
            return response.json();
        })
        .then(subjects => {
            const select = document.getElementById('subject');

            subjects.forEach(subject => {
                const option = document.createElement('option');
                option.value = subject.id;
                option.textContent = subject.name;
                select.appendChild(option);
            });
        })
        .catch(err => {
            console.error(err);
        });


  

    const form = document.getElementById('search-form');
    const tbody = document.querySelector('#results tbody');
    const status = document.getElementById('status');
    const button = form.querySelector('button');   
    const subjectSelect = document.getElementById('subject');
    const availableCheckbox = document.getElementById('available');

  
    fetchBooks();

  
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        fetchBooks();
    });

   
    subjectSelect.addEventListener('change', fetchBooks);
    availableCheckbox.addEventListener('change', fetchBooks);

    function fetchBooks() {

        const params = new URLSearchParams(new FormData(form));

      
        button.disabled = true;
        status.textContent = 'Searching…';

        fetch('books.php?' + params.toString())
            .then(response => {
                if (!response.ok) {
                    throw new Error('Could not load books.');
                }
                return response.json();
            })
            .then(books => {

                tbody.replaceChildren();

                if (books.length === 0) {
                    status.textContent = 'No books match your search.';
                    return;
                }

                books.forEach(book => {
                    const tr = document.createElement('tr');

                    const tdTitle = document.createElement('td');
                    tdTitle.textContent = book.title;

                    const tdAuthor = document.createElement('td');
                    tdAuthor.textContent = book.author;

                    const tdYear = document.createElement('td');
                    tdYear.textContent = book.year;

                    const tdSubject = document.createElement('td');
                    tdSubject.textContent = book.subject;

                    const tdCopies = document.createElement('td');
                    tdCopies.textContent = `${book.available} of ${book.copies}`;

                    if (book.available === 0) {
                        tdCopies.classList.add('out');
                    }

                    tr.append(tdTitle, tdAuthor, tdYear, tdSubject, tdCopies);
                    tbody.appendChild(tr);
                });

                if (books.length === 1) {
                    status.textContent = '1 book found.';
                } else {
                    status.textContent = `${books.length} books found.`;
                }
            })
            .catch(err => {
                console.error(err);
                tbody.replaceChildren();
                status.textContent = "Couldn't load the books. Please try again.";
            })
            .finally(() => {
                button.disabled = false;
            });
    }
});
