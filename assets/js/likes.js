// Modal için gerekli JavaScript kodları
document.querySelectorAll('.like-count').forEach(item => {
    item.addEventListener('click', event => {
        const id = event.target.getAttribute('data-id');
        const type = event.target.getAttribute('data-type');

        fetch(`get_likes.php?id=${id}&type=${type}`)
            .then(response => response.json())
            .then(data => {
                const likeList = document.getElementById('likeList');
                likeList.innerHTML = '';
                data.likes.forEach(user => {
                    const listItem = document.createElement('li');
                    listItem.textContent = user.username;
                    likeList.appendChild(listItem);
                });

                document.getElementById('likeModal').style.display = 'block';
            });
    });
});

document.querySelector('.close').onclick = function() {
    document.getElementById('likeModal').style.display = 'none';
};

window.onclick = function(event) {
    if (event.target == document.getElementById('likeModal')) {
        document.getElementById('likeModal').style.display = 'none';
    }
};