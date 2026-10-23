<html lang="ES" style="width:100%;height:100%;padding:0px;margin:0px">
    <head>
    	<title>LABS - EXAMEN</title>
    </head>
    <body style="width:100%;height:100%;padding:0px;margin:0px">
        @php 
            //dd($url);
            $html="#";
            foreach($url as $key=>$x){
                foreach($x as $s){
                    $html=$s;
                }
               
            }
          
        @endphp
        
        <iframe src="https://docs.google.com/viewerng/viewer?url={{$html}}" ></iframe>
    </body>
</html>