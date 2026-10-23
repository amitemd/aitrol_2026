<style>
.ir-arriba {
    display:none;
    padding:20px;
    cursor:pointer;
	position: fixed;
	bottom:20px;
    font-size: 20px;
	right:20px;
    background: #007bff;
    font-size:10px;
    color: white;
}
</style>
<footer class="main-footer hero-footer"><strong>Copyright &copy; {{date('Y')}} <a href="https://mdconsgroup.com/">MdConsgroup</a>.</strong><span class="botones">All rights reserved.</span>
    <div class="float-right d-none d-sm-inline-block botones"><b>Version</b> {{config('data.info')->version_info.'.'.config('data.info')->mayor_info.'.'.config('data.info')->menor_info}}</div>
</footer>
<span class="ir-arriba fa fa-arrow-up"></span>