<?php 
include "head.php"; 

if (isset($_SESSION['idagencias'])){
	$idagencias = $_SESSION['idagencias'];
	$current = 'Edit';
	$sqlQuery = "SELECT * ";
	$sqlQuery .= " FROM `agencias`  ";
	$sqlQuery .= " WHERE 1 ";
	$sqlQuery .= " AND idagencias = ".$idagencias;
	$resultadoStringSQL = resultFromQuery($sqlQuery);
	if ($row = siguienteResult($resultadoStringSQL)){
		$idagencias = $row->idagencias;
		$nombre = $row->nombre;
		$telefono = $row->telefono;
	}
}
?>

<!--main-container-part-->
<div id="content">
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> 
		<a href="index.php" title="Home" class="tip-bottom"><i class="icon-home"></i> Home</a> 
		<a href="administradores.php" title="Administradores" class="tip-bottom">Administradores</a>
		<a href="administradores.agencias.php" title="agencias" class="tip-bottom">Agencias</a>
		<a href="#" class="current">Edit...</a>
	</div>
  </div>
<!--End-breadcrumbs-->
  <div class="container-fluid">
	<div class="row-fluid">
		<div class="span12">
			<form action="posts.php" id="form" name="form" class="form-horizontal" method="post">
			<div class="widget-box">
				<div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
					<h5>Edit Agencia</h5>
				</div>
				<div class="widget-content nopadding">
					<input type="hidden" id="accion" name="accion" value="admitirAgencias" />
					<input type="hidden" id="idagencias" name="idagencias" value="<?php echo $idagencias;?>" />
					<div class="control-group">
						<label class="control-label">Nombre</label>
						<div class="controls">
							<input id="nombre" name="nombre" type="text" class="span11" placeholder="nombre" required="true" value="<?php echo $nombre;?>"/>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Telefono</label>
						<div class="controls">
							<input id="telefono" name="telefono" type="text" class="span11" placeholder="telefono" value="<?php echo $telefono;?>"/>
						</div>
					</div>
					<div id="status"></div>
				</div>
			</div>
			<div class="class="form-actions">
				<button class="btn btn-success" type="submit">Modificar</button>
			</div>
			</form>
		</div>
	</div>
    <hr/>
  </div>
</div>

<!--end-main-container-part-->

<?php include "footer.php"; ?>
