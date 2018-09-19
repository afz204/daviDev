<html>
<head>
<style type="text/css">
	.logo{ padding:3px;width: 400px;height: auto; border: 0px solid black; overflow: hidden;}
	.print-area { font-family: "Times New Roman", Times, serif;}
	.logo{ margin-right: 50% }
	.fromto{ padding:5px;border:1px solid black; width: 400px;height: auto;margin-left: 50%;  overflow: hidden;}
	.alamat{height: 100px}
	.to{height: 45px;}
</style>

</head>

<body>
<div id="print-area-1" class="print-area">
    <div class="logo">
    <img src="../../assets/images/logo.png" width="350px"><br>
	    Jl. Sasak II. NO.6 RT.2/RW.2 Klp. Dua. Kb. Jeruk, Kota Jakarta Barat, DKI Jakarta 11550<br>
		Telp : 0811133364  / 021-22530466<br>
		Email : bungadavi@gmail.com / order@bungadavi.co.id<br>
	</div>

	<div class="fromto">
	
		<div class="to">
		To : {{ $amplop->name_pengirim }}
		</div>
		<div class="alamat">
				{{ $amplop->alamat_pengirim }}
		</div>
	</div>

</div>
<div class="clearfix"></div>
</body>

</html>