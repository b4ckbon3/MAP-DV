<?php include "head.php"; ?>

<!--main-container-part-->
<div id="content">
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> 
		<a href="index.php" title="Home" class="tip-bottom"><i class="icon-home"></i> Home</a> 
		<a href="administradores.php" title="Administradores" class="tip-bottom">Administradores</a>
		<a href="#" class="current">Listas de precios</a>
	</div>
	<h1>Listas de precios</h1><hr>
  </div>
<!--End-breadcrumbs-->
  <div class="container-fluid">
	<div class="row-fluid">
        <div class="widget-box">
			<div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
				<h5>Listas de precios</h5>
			</div>
			<form id="ListasdepreciosForm" name="ListasdepreciosForm" action="posts.php" method="post">
			<div class="widget-content nopadding">
				<?php
					$sqlQuery = " SELECT LDP.idlistasdeprecios 'ID' , LDP.nombre 'Nombre' ";
					$sqlQuery .= " , RDP.plural 'Responsables', LDP.VigenciaIN, LDP.VigenciaOUT  ";
					$sqlQuery .= ", CONCAT ('<a  class=btn href=administradores.listasdeprecios.precios.paso01.php?id=', LDP.idlistasdeprecios ,'>Administrar precios</a>') 'Adm. precios.'";
					// $sqlQuery .= " ()  ";
					$sqlQuery .= " FROM `listasdeprecios` LDP ";
					$sqlQuery .= " LEFT JOIN `responsablesDePago` RDP ON LDP.idresponsablesDePago = RDP.idresponsablesDePago ";


					$sqlQuery .= " WHERE 1 ";
					
					echo tableFromResult(resultFromQuery($sqlQuery), 'Listasdeprecios', false, true, 'posts.php', true);
				?>		  
			</div>
			</form>
        </div>
		<form method="post" action="posts.php">
			<input type="hidden" id="accion" name="accion" value="ListasdepreciosNew" />
			<button class="btn btn-success" type="submit">Novo...</button>
		</form>      	
	</div>
    <hr/>
  </div>
</div>
<!--end-main-container-part-->
<?php include "footer.php"; ?>