<div class="row">
	<div class="col-md-12">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
		<img id="imafoto" src="{{asset($foto->ruta.$foto->archivo)}}" alt="{{$foto->tipo_documento}}" style="max-width: 900px;">
	</div>
</div>
<script>
 
        $(document).ready(function() {
	        $("#imafoto").mlens(
	        {
	            imgSrc: $("#imafoto").attr("data-big"),   // path of the hi-res version of the image
	            lensShape: "circle",                // shape of the lens (circle/square)
	            lensSize: 200,                  // size of the lens (in px)
	            borderSize: 1,                  // size of the lens border (in px)
	            borderColor: "#000000",                // color of the lens border (#hex)
	            zoomLevel: 2,  
	            borderRadius: 0,                // border radius (optional, only if the shape is square)
	            imgOverlay: $("#imafoto").attr("data-overlay"), // path of the overlay image (optional)
	            overlayAdapt: true, // true if the overlay image has to adapt to the lens size (true/false)
	            responsive: true 
	        });
        });
</script>