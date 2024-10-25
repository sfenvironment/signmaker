const MAX_COUNT = 8;
const ITEMS_PER_ROW = 4;
function recalculate() {
    const imageMenu = document.getElementById('image-menu');
    const imageRegion = document.getElementById('image-region');
    Array.from(imageRegion.children).forEach(img => imageRegion.removeChild(img));
    imageRegion.className = '';
    let childCount = 0;
    let itemsInRow = 0;
    let currentRow = document.createElement('div');
    currentRow.className = 'row';
    imageRegion.appendChild(currentRow);
    Array.from(imageMenu.childNodes).forEach(function (li) {
        if ('false' === li.dataset.chosen) return;
        childCount++;
        const url = li.dataset.url;
        const sticker = document.createElement('span');
        sticker.style.backgroundImage = `url("${url}")`;
        currentRow.appendChild(sticker);
        itemsInRow++;
        if (itemsInRow === ITEMS_PER_ROW) {
            currentRow = document.createElement('div');
            currentRow.className = 'row row-2';
            imageRegion.appendChild(currentRow);
        }
    });
    imageRegion.className = imageMenu.className = `children-${childCount}`;
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
            if (li.dataset.chosen === 'false') {
                const countSelected = document.querySelectorAll('#image-menu li[data-chosen="true"]').length;
                if (countSelected >= MAX_COUNT) {
                    alert('Max items chosen');
                    return;
                }
            }
            li.dataset.chosen = li.dataset.chosen === 'true' ? 'false' : 'true';
            recalculate();
        });
        imageMenu.appendChild(li);
    });

    document.getElementById('generate').addEventListener('click', function (event) {
        event.preventDefault();
        const sign = document.getElementById('sign');
        let headers = new Headers();
        headers.append('Content-Type', 'application/json');
        fetch(window.location.pathname, {
            method: 'POST',
            headers,
            body: JSON.stringify({
                html: document.getElementsByTagName('html')[0].outerHTML
            })
        })
            .then(async res => ({
                filename: 'signmaker.pdf',
                blob: await res.blob()
            }))
            .then(resObj => {
                // It is necessary to create a new blob object with mime-type explicitly set for all browsers except Chrome, but it works for Chrome too.
                const newBlob = new Blob([resObj.blob], { type: 'application/pdf' });

                // MS Edge and IE don't allow using a blob object directly as link href, instead it is necessary to use msSaveOrOpenBlob
                if (window.navigator && window.navigator.msSaveOrOpenBlob) {
                    window.navigator.msSaveOrOpenBlob(newBlob);
                } else {
                    // For other browsers: create a link pointing to the ObjectURL containing the blob.
                    const objUrl = window.URL.createObjectURL(newBlob);

                    let link = document.createElement('a');
                    link.href = objUrl;
                    link.download = resObj.filename;
                    link.click();

                    // For Firefox it is necessary to delay revoking the ObjectURL.
                    setTimeout(() => { window.URL.revokeObjectURL(objUrl); }, 250);
                }
            })
            .catch((error) => {
                console.log('DOWNLOAD ERROR', error);
            });
    });

    document.getElementById('reset').addEventListener('click', function (event) {
        event.preventDefault();
        Array.from(document.querySelectorAll('#image-menu li[data-chosen="true"]')).forEach(function (image) {
            image.dataset.chosen = 'false';
        });
        recalculate();
    })
});