function recalculate() {
    const imageMenu = document.getElementById('image-menu');
    const imageRegion = document.getElementById('image-region');
    Array.from(imageRegion.children).forEach(img => imageRegion.removeChild(img))
    Array.from(imageMenu.childNodes).forEach(function (li) {
        if ('false' === li.dataset.chosen) return;
        const url = li.dataset.url;
        const sticker = document.createElement('li');
        const img = document.createElement('img');
        img.src = url;
        sticker.appendChild(img);
        imageRegion.appendChild(sticker);
    });
}
document.addEventListener('DOMContentLoaded', function () {
    const imageMenu = document.getElementById('image-menu');
    configuration.images.forEach(function (image) {
        const url = configuration.directory + image;
        const img = document.createElement('img');
        const li = document.createElement('li');
        img.src = li.dataset.url = url;
        li.dataset.chosen = 'false';
        li.appendChild(img);
        li.addEventListener('click', function (event) {
            li.dataset.chosen = li.dataset.chosen === 'true' ? 'false' : 'true';
            recalculate();
        });
        imageMenu.appendChild(li);
    });

    document.getElementById('generate').addEventListener('click', function (event) {
        event.preventDefault();
        const sign = document.getElementById('sign');
    });
});