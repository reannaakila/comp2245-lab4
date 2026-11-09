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

            subjects.forEach(sub => {
                const option = document.createElement('option');
                option.value = subject.id;
                option.textContent = subject.name;
                select.appendChild(option);
            });
        })
        .catch(err => {
            console.error(err);
        });

});
