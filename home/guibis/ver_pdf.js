

  document.getElementById('clear-cookies-btn').addEventListener('click', function() {
    var bookName = document.getElementById('codigo_libro_cache').value;
    var nombre_real = document.getElementById('nombre_libro').value;
    console.log(nombre_real);
    clearBookStateCookies(bookName);
    alert('La memoria ha sido eliminada, recarga para vizualisar nuevamente '+nombre_real+' .');
});

function clearBookStateCookies(bookName) {
    var encodedBookName = encodeURIComponent(bookName).replace(/\s/g, '_');
    document.cookie = 'currentPage_' + encodedBookName + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/';
    document.cookie = 'currentZoom_' + encodedBookName + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/';
}



document.addEventListener('DOMContentLoaded', function () {
    var bookName = document.getElementById('codigo_libro_cache').value;
    var pdf_libro = document.getElementById('pdf_libro').value;
    var pdfjsLib = window['pdfjs-dist/build/pdf'];
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'guibis/worker.js';

    var loadingTask = pdfjsLib.getDocument(pdf_libro);
    var pdfDoc = null;
    var pageNum = getPageFromCookie(bookName);
    var scale = getZoomFromCookie(bookName);
    $("#page-number").val(pageNum);
    $("#paginas").val(pageNum);
    $("#zoom-slider").val(scale);
    var renderTask = null;

    function getCookieExpiration() {
        var date = new Date();
        date.setMonth(date.getMonth() + 6); // Añadir 6 meses a la fecha actual
        return "expires=" + date.toUTCString() + ";";
    }

    function setDocumentState(pageNum, bookName, scale) {
        var encodedBookName = encodeURIComponent(bookName).replace(/\s/g, '_');
        var expiration = getCookieExpiration();
        var creationDate = new Date().toISOString(); // Obtiene la fecha y hora actual en formato ISO

        // Guardar la información del estado del documento
        document.cookie = 'currentPage_' + encodedBookName + '=' + pageNum + ';' + expiration + 'path=/';
        document.cookie = 'currentZoom_' + encodedBookName + '=' + scale + ';' + expiration + 'path=/';

        // Guardar la fecha de creación
        document.cookie = 'creationDate_' + encodedBookName + '=' + creationDate + ';' + expiration + 'path=/';
    }

    function getPageFromCookie(bookName) {
        var encodedBookName = encodeURIComponent(bookName).replace(/\s/g, '_');
        var pageCookie = document.cookie.split(';').find(cookie => cookie.trim().startsWith('currentPage_' + encodedBookName + '='));
        return pageCookie ? parseInt(pageCookie.split('=')[1].trim()) : 1;
    }

    function getZoomFromCookie(bookName) {
        var encodedBookName = encodeURIComponent(bookName).replace(/\s/g, '_');
        var zoomCookie = document.cookie.split(';').find(cookie => cookie.trim().startsWith('currentZoom_' + encodedBookName + '='));
        return zoomCookie ? parseFloat(zoomCookie.split('=')[1].trim()) : 1.5;
    }

    function showLoader() {
        document.querySelector('.notificacion_agregar_imagenes').innerHTML = '<div class="notificacion_negativa">' +
        '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>' +
        '</div>';
    }

    function hideLoader() {
        document.querySelector('.notificacion_agregar_imagenes').innerHTML = '';
    }

    function clearBookStateCookies(bookName) {
        var encodedBookName = encodeURIComponent(bookName).replace(/\s/g, '_');
        document.cookie = 'currentPage_' + encodedBookName + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/';
        document.cookie = 'currentZoom_' + encodedBookName + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/';
    }

    function handleRenderingError(error) {
        console.error('Error rendering page: ', error);
         alert('Error al cargar código: ' + error.message);
        if (error.message.includes('net::ERR_CACHE_OPERATION_NOT_SUPPORTED')) {
            clearBookStateCookies(bookName);
            location.reload(); // Recarga la misma página para intentar resolver el problema
        }
    }

    loadingTask.promise.then(function(pdfDoc_) {
        pdfDoc = pdfDoc_;
        document.getElementById('page_count').textContent = pdfDoc.numPages;
        renderPage(pageNum, scale, bookName);
    }, handleRenderingError);

    function renderPage(num, scale, bookName) {
        if (pdfDoc === null) {
            return;
        }
        showLoader();
        pdfDoc.getPage(num).then(function(page) {
            var viewport = page.getViewport({scale: scale * window.devicePixelRatio});
            var canvas = document.getElementById('the-canvas');
            var ctx = canvas.getContext('2d');
            canvas.width = viewport.width;
            canvas.height = viewport.height;

            canvas.style.width = viewport.width / window.devicePixelRatio + 'px';
            canvas.style.height = viewport.height / window.devicePixelRatio + 'px';

            if (renderTask) {
                renderTask.cancel();
            }

            var renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };

            renderTask = page.render(renderContext);
            renderTask.promise.then(function() {
                hideLoader();
                console.log('Page rendered');
            }).catch(handleRenderingError);
        });

        document.getElementById('page_num').textContent = num;
        setDocumentState(num, bookName, scale);
    }

    document.getElementById('prev').addEventListener('click', function() {
        if (pageNum <= 1) {
            return;
        }
        pageNum--;
        $("#page-number").val(pageNum);
        $("#paginas").val(pageNum);
        renderPage(pageNum, scale, bookName);
    });



    document.getElementById('next').addEventListener('click', function() {
        if (pageNum >= pdfDoc.numPages) {
            return;
        }
        pageNum++;
        $("#page-number").val(pageNum);
        $("#paginas").val(pageNum);
        renderPage(pageNum, scale, bookName);
    });


    document.getElementById('prev_abajo').addEventListener('click', function() {
        if (pageNum <= 1) {
            return;
        }
        pageNum--;
        $("#page-number").val(pageNum);
        $("#paginas").val(pageNum);
        renderPage(pageNum, scale, bookName);
    });



    document.getElementById('next_abajo').addEventListener('click', function() {
        if (pageNum >= pdfDoc.numPages) {
            return;
        }
        pageNum++;
        $("#page-number").val(pageNum);
        $("#paginas").val(pageNum);
        renderPage(pageNum, scale, bookName);
    });


    document.getElementById('zoom-slider').addEventListener('input', function() {
        scale = parseFloat(this.value);
        renderPage(pageNum, scale, bookName);
    });

    document.getElementById('go-to-page').addEventListener('click', function() {
        var requestedPage = parseInt(document.getElementById('page-number').value);
        if (isNaN(requestedPage) || requestedPage < 1 || requestedPage > pdfDoc.numPages) {
            alert("Número de página fuera de rango. Por favor ingresa un número entre 1 y " + pdfDoc.numPages);
            return;
        }
        pageNum = requestedPage;
        renderPage(pageNum, scale, bookName);
    });

    var initialDistance = null;

    function getDistance(touches) {
        return Math.sqrt(
            (touches[0].pageX - touches[1].pageX) ** 2 +
            (touches[0].pageY - touches[1].pageY) ** 2
        );
    }

    document.getElementById('the-canvas').addEventListener('touchstart', function (e) {
        if (e.touches.length == 2) {
            e.preventDefault();
            initialDistance = getDistance(e.touches);
        }
    }, { passive: false });

    document.getElementById('the-canvas').addEventListener('touchmove', function (e) {
        if (e.touches.length == 2) {
            e.preventDefault();
            var newDistance = getDistance(e.touches);
            if (initialDistance != null && newDistance != null) {
                if (newDistance > initialDistance) {
                    scale *= 1.05;
                } else {
                    scale /= 1.05;
                }
                renderPage(pageNum, scale, bookName);
                initialDistance = newDistance;
            }
        }
    }, { passive: false });

    document.getElementById('the-canvas').addEventListener('touchend', function (e) {
        if (e.touches.length < 2) {
            initialDistance = null;
        }
    });
});
