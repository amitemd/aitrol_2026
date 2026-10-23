<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0"> -->
    <title>PDF</title>
</head>
<style>
    #canvas_container {
        /* width: 800px;
        height: 450px; */
        overflow: auto;
        width: 100%;
        position: relative;
        height: 100vh;
    }

    /* #canvas_container {
        background: #333;
        text-align: center;
        border: solid 3px;
    } */

    .button {
        background: #009abf;
        border: none;
        border-radius: 10%;
        padding: 5px;
        color: white;
    }

    .pdf_renderer {
        /* position: absolute; */
        z-index: 1;
        width: 100%;
        margin-bottom: 10px;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }

    #zoom_controls {
        position: fixed;
        z-index: 99999;
        display: grid;
        width: 20px;
        gap: 10px;
    }

    /* HTML: <div class="loader"></div> */
    /* HTML: <div class="loader"></div> */
    .loader {
        width: 30px;
        padding: 5px;
        aspect-ratio: 1;
        border-radius: 50%;
        background: #25b09b;
        --_m:
            conic-gradient(#0000 10%, #000),
            linear-gradient(#000 0 0) content-box;
        -webkit-mask: var(--_m);
        mask: var(--_m);
        -webkit-mask-composite: source-out;
        mask-composite: subtract;
        animation: l3 1s infinite linear;
    }

    @keyframes l3 {
        to {
            transform: rotate(1turn)
        }
    }
</style>

<body style="margin: 0; padding: 0; border: 0;">
    <div id="my_pdf_viewer">
        <div id="canvas_container">
            <div style="
                text-align: center;
                display: flex;
                justify-content: center;
                padding: 20px;
            ">
                <div id="loader" class="loader"></div>

            </div>
            <canvas class="pdf_renderer" id="pdf_renderer"></canvas>
            <div id="container_pages"></div>
            <!-- <div id="zoom_controls">
                <button class="button" id="zoom_in">


                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-zoom-in">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                        <path d="M7 10l6 0"></path>
                        <path d="M10 7l0 6"></path>
                        <path d="M21 21l-6 -6"></path>
                    </svg>



                </button>
                <button class="button" id="zoom_out">



                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-zoom-out">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                        <path d="M7 10l6 0"></path>
                        <path d="M21 21l-6 -6"></path>
                    </svg>


                </button>
            </div> -->
<!-- 
            <div id="navigation_controls">
                <button id="go_previous">Previous</button>
                <input id="current_page" value="1" type="number" />
                <button id="go_next">Next</button>
            </div> -->
        </div>
    </div>

</body>

</html>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.0.943/pdf.min.js"></script>

<script>
    const pdfUrl = `@if($file == "") {{ $url}} @else {{ $file }} @endif`
    // const pdfUrl = "https://ieced.siaam.ec/sis_medico/public/bd/pdf/receta/186441/2"
    var myState = {
        pdf: null,
        currentPage: 1,
        zoom: 2.5
    }
    pdfjsLib.getDocument(pdfUrl).then((pdf) => {
        myState.pdf = pdf;
        render();

    });

    function render() {
        myState.pdf.getPage(myState.currentPage).then((page) => {
            var canvas = document.getElementById("pdf_renderer");
            const loader = document.getElementById('loader');

            if (loader) {
                loader.remove();
            }
            // loader.style.display = 'none'; // Ocultar el loader cuando la página se haya cargado correctamente
            var ctx = canvas.getContext('2d');
            var viewport = page.getViewport(myState.zoom);
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            page.render({
                canvasContext: ctx,
                viewport: viewport
            });
        });

        createCurrentPage()
    }

    // document.getElementById('go_previous')
    //     .addEventListener('click', (e) => {
    //         if (myState.pdf == null ||
    //             myState.currentPage == 1) return;
    //         myState.currentPage -= 1;
    //         document.getElementById("current_page")
    //             .value = myState.currentPage;
    //         render();
    //     });

    // document.getElementById('go_next')
    //     .addEventListener('click', (e) => {
    //         if (myState.pdf == null || myState.currentPage > myState.pdf._pdfInfo.numPages)
    //             return;

    //         myState.currentPage += 1;
    //         document.getElementById("current_page")
    //             .value = myState.currentPage;
    //         render();
    //     });

    // document.getElementById('current_page')
    //     .addEventListener('keypress', (e) => {
    //         if (myState.pdf == null) return;

    //         // Get key code 
    //         var code = (e.keyCode ? e.keyCode : e.which);

    //         // If key code matches that of the Enter key 
    //         if (code == 13) {
    //             var desiredPage =
    //                 document.getElementById('current_page')
    //                 .valueAsNumber;

    //             if (desiredPage >= 1 &&
    //                 desiredPage <= myState.pdf
    //                 ._pdfInfo.numPages) {
    //                 myState.currentPage = desiredPage;
    //                 document.getElementById("current_page")
    //                     .value = desiredPage;
    //                 render();
    //             }
    //         }
    //     });

    // //crear mas canvas para las demas paginas
    const createCurrentPage = () => {
        let counter = myState.pdf._pdfInfo.numPages;
        let container = document.getElementById("container_pages")
        if(counter == 1) return

        for (let i = 2; i <= counter; i++) {
            let canvas = document.createElement("canvas");
            canvas.classList.add("pdf_renderer")
            canvas.id = `page_${i}`;
            container.appendChild(canvas);

            myState.pdf.getPage(i).then((page) => {
                var viewport = page.getViewport(myState.zoom);
                let canvas = document.getElementById(`page_${i}`);
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                page.render({
                    canvasContext: canvas.getContext('2d'),
                    viewport: viewport
                })
            })

        }


    }
    // document.getElementById('zoom_in')
    //     .addEventListener('click', (e) => {
    //         if (myState.pdf == null) return;
    //         myState.zoom += 0.05;
    //         render();
    //     });
    // document.getElementById('zoom_out')
    //     .addEventListener('click', (e) => {
    //         if (myState.pdf == null) return;
    //         myState.zoom -= 0.05;
    //         render();
    //     });
</script>