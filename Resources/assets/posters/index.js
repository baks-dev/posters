/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

const collectionHolder = document.querySelector("[data-collection-holder]");
const addButton = document.getElementById("add-text-btn");
let index = collectionHolder.querySelectorAll(".collection-item").length;

addButton.addEventListener("click", () =>
{
    const prototype = collectionHolder.dataset.prototype;
    const newForm = prototype.replace(/__name__/g, index);

    const wrapper = document.createElement("div");
    wrapper.className = "collection-item";
    wrapper.innerHTML = newForm;

    collectionHolder.appendChild(wrapper);
    index++;
});

document.addEventListener("click", function(e)
{
    if(e.target && e.target.classList.contains("remove-text"))
    {
        e.preventDefault();
        const collectionItem = e.target.closest(".collection-item");
        if(collectionItem)
        {
            collectionItem.remove();
        }
    }
});

/** DragNDrop для загрузки изображения */

/* Блок для вреппера */
let $blockImage = document.getElementById("image_wrapper");

/** Предотвратить стандартное (по умолчанию) поведение для событий: 'dragenter', 'dragover', 'dragleave', 'drop' */
["dragenter", "dragover", "dragleave", "drop"].forEach(event =>
{
    $blockImage.addEventListener(event, function(e)
        {
            e.preventDefault();
            e.stopPropagation();
        },
        false,
    );
});

/** Подсветить photo_collection при перетаскивании при событиях 'dragenter', 'dragover' */
["dragenter", "dragover"].forEach(event =>
{
    $blockImage.addEventListener(event, () =>
    {
        $blockImage.classList.add("shadow");
    }, false);
});

/** Удалить класс подсветки при событиях 'dragleave', 'drop' */
["dragleave", "drop"].forEach(event =>
{
    $blockImage.addEventListener(event, () =>
    {
        $blockImage.classList.remove("shadow");
    }, false);
});

/** Обработать событие drop */
$blockImage.addEventListener("drop", function(e)
    {
        const dt = e.dataTransfer;
        const files = dt.files;

        /* Обработать файлы полученные при "перетягивании" */
        ([...files]).forEach(previewAndAttachFile);

    },
);

/** Отобразить и загрузить в file input */

function previewAndAttachFile(file)
{
    /* Проверить это файл является изображением */
    if(!file.type.startsWith("image/"))
    {
        return;
    }

    const reader = new FileReader();
    reader.readAsDataURL(file);

    /* После того как файл "загрузился" в объект FileReader */
    reader.onloadend = function()
    {

        /* Отобразить полученный file в качестве background-image для label */
        $blockImage.querySelector("label").style.backgroundImage = `url('${reader.result}')`;

        /* Получить соотв-щий input type=file */
        const fileInput = $blockImage.querySelector("input[type=\"file\"]");


        /** Загрузить файл в input type=file */

        /* Создать объект DataTransfer и добавить в него файл */
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);

        /* Присвоить файлы объекта DataTransfer свойству 'files' поля загрузки файла. */
        fileInput.files = dataTransfer.files;

    };
}

/** END DragNDrop */
