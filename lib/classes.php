<?php
error_reporting(E_ALL);

class ItemGenerico {
	var $id;
	var $nombre;
	var $descripcion;
}

class ListaGenerica extends ItemGenerico {
  var $lista;
  
  function initializeLista() {
    $this->lista = Array();
  }
  
  function initialize() {
    $this->initializeLista();
  }
  
  function add(&$item, $repeat = true) {
		if (!$repeat) {
			if (!$this->includes($item)) {
		    $this->lista[] = $item;
			}
		} else {
	    $this->lista[] = $item;
		}
		
		return $this->count();

  }
  
  function remove(&$item) {
		for ($i = 0; $i < $this->count(); $i++) {
			if ($this->at($i) === $item) {
				$this->removeAt($i);
			}
		}
  }
  
  function removeAt($index) {
 	  $lista = Array();
 	  for ($i = 0; $i < $this->count(); $i++) {
 	  	  if ($i != $index) {
  	  	  $lista[] = $this->at($i);
 	  	  }
 	  }
 	  $this->lista = $lista;
  }
  
  function first() {
    return $this->lista[0];
  }
  
  function last() {
    return $this->lista[$this->count()-1];
  }
  
  function asArray() {
    $result = Array();
    for ($i = 0; $i < sizeOf($this->lista); $i++) {
      $result[] = $this->lista[$i]->descripcion;
    }
    return $result;
  }
  
  function asArrayComplejo() {
    $result = Array();
    $result["id"] = Array();
    for ($i = 0; $i < sizeOf($this->lista); $i++) {
      $item = $this->lista[$i];
      $result["id"][] = $item->id;
      $result[$item->id] = $item->descripcion;
    }
    return $result;
  }
  
  function count() {
    return sizeOf($this->lista);
  }
  
  function at($index) {
    return $this->lista[$index];
  }
  
  function conNombre($nombre) {
  	  for ($i = 0; $i < $this->count(); $i++) {
  	  	  $item = $this->at($i);
  	  	  if ($item->nombre == $nombre) {
  	  	  	  return $item;
  	  	  }
  	  }
  	  return null;
  }
  
  function conID($ID) {
  	  for ($i = 0; $i < $this->count(); $i++) {
  	  	  $item = $this->at($i);
  	  	  if ($item->id == $ID) {
  	  	  	  return $item;
  	  	  }
  	  }
  	  return null;
  }
  
  function includes(&$unItem) {
    // Devuelve true si unItem es parte de los items del receptor
    for ($i = 0; $i < $this->count(); $i++) {
      if ($this->lista[$i] === $unItem) {
        return true;
      }
    }
    return false;
  }
  
  function nuevo() {
  	  $item = new ItemGenerico;
  	  $this->add($item);
  	  return $item;
  }

	function copyAllFrom(&$lista, $repeat = true) {
    for ($i = 0; $i < $lista->count(); $i++) {
			$item = $lista->at($i);
			$this->add($item, $repeat);
    }
	}	

	function addAllFrom(&$lista, $repeat = true) {
    for ($i = 0; $i < $lista->count(); $i++) {
			$item = &$lista->at($i);
			$this->add($item, $repeat);
    }
	}	

	function copy($referenced = true) {
		$lista = new ListaGenerica;
		$lista->initialize();
		if ($referenced) {
			$lista->addAllFrom($this);
		} else {
			$lista->copyAllFrom($this);
		}
	}
	
	function isEmpty() {
		return $this->count() == 0;
	}
  
  function asHTMLString() {
    $result = $this->nombre." (".$this->count().") <BR>";
    for ($i = 0; $i < sizeOf($this->lista); $i++) {
    	$result .= "id: ".$this->lista[$i]->id."	| nombre: ".$this->lista[$i]->nombre."	| descripcion: ".$this->lista[$i]->descripcion."<BR>";
    }
    return $result;
  }
  
  function asSQLIDString() {
  	$result = '';
  	for ($i = 0; $i < $this->count(); $i++) {
  		$item = &$this->at($i);
  		$result .= $item->id;
  		if ($i < $this->count()-1) {
  			$result .= ', ';
  		}
  	}
  	return $result;
  }
  
}

class Usuario extends ItemGenerico {
  var $clave;
  var $eMail;
  var $permisos;
	var $empleado;
  
  function initializePermisos() {
		$this->permisos = new ListaGenerica;
		$this->permisos->initialize();
  }
	
  function initializeEmpleado() {
		$this->empleado = new Empleado;
		$this->empleado->initialize();
  }
	
	function initialize() {
    $this->id = 0;
    $this->nombre = '';
    $this->clave = '';
    $this->descripcion = '';
    $this->initializePermisos();
    $this->initializeEmpleado();
  }
  
  function nombreCompleto() {
  	if (isset($this->empleado->id)) {
  		return $this->empleado->nombreCompleto();
  	} else {
  		return $this->nombre;
  	}
  }
}

class ItemDeEvaluacion extends ItemGenerico {
	var $ponderacion;
	var $evaluacion;
	var $autoevaluacion;
	var $estado;
	var $subitems;
	var $acciones;
	var $datosExtra;
	var $comentarios;
	
	function initialize() {
		$this->acciones = new ListaGenerica;
		$this->acciones->initialize();
		$this->subitems = new ListaGenerica;
		$this->subitems->initialize();
		$this->datosExtra = new ListaGenerica;
		$this->datosExtra->initialize();
		$this->comentarios = new ListaGenerica;
		$this->comentarios->initialize();
		
	}
	
	function estadoDeCompletitudDeAcciones() {
		$cantidadDeAcciones = $this->acciones->count();
		$cantidadDeAccionesTerminadas = 0;
		for ($nroAccion = 0; $nroAccion < $cantidadDeAcciones; $nroAccion++) {
			$accion = &$this->acciones->at($nroAccion);
			if ($accion->estado->nombre == 'terminada') {
				$cantidadDeAccionesTerminadas++;
			}
		}

		if ($cantidadDeAcciones == $cantidadDeAccionesTerminadas) {
			return 2;
		} elseif ((0 < $cantidadDeAccionesTerminadas) && ($cantidadDeAccionesTerminadas < $cantidadDeAcciones)) {
			return 1;
		} else {
			return 0;
		}

	}
	
	function renombrarAcciones() {
		for ($nroAccion = 0; $nroAccion < $this->acciones->count(); $nroAccion++) {
			$accion = &$this->acciones->at($nroAccion);
			$accion->nombre = $this->nombre.':accion'.($nroAccion+1);
		}
	}
	
	function nombreParaProximaAccion() {
		return $this->nombre.':accion'.($this->acciones->count()+1);
	}
	
	function addAccion(&$accion) {
		$accion->nombre = $this->nombreParaProximaAccion();
		$this->acciones->add($accion);
	}
	
	function initializeDatosExtra($tipoDeObjetivo) {
		$datoExtra = new DatoExtraDeItem;
		$datoExtra->id = -1;
		$datoExtra->nombre = 'fechaPropuesta';
		$datoExtra->tipoDeDato = 4;
		$datoExtra->descripcion = 'Fecha propuesta';
		$datoExtra->valor = date('Y-m-d');
		$this->datosExtra->add($datoExtra);
		if ($tipoDeObjetivo == 'objetivoCuantitativo') {
			// Datos extra para objetivosCuantitativos
			$datoExtra = new DatoExtraDeItem;
			$datoExtra->id = -1;
			$datoExtra->nombre = 'objetivo';
			$datoExtra->tipoDeDato = 3;
			$datoExtra->descripcion = 'Objetivo';
			$this->datosExtra->add($datoExtra);
		}
		$datoExtra = new DatoExtraDeItem;
		$datoExtra->id = -1;
		$datoExtra->nombre = 'indicadores';
		$datoExtra->tipoDeDato = 3;
		$datoExtra->descripcion = 'Indicadores de desempe�o';
		$this->datosExtra->add($datoExtra);
	}
}

class DatoExtraDeItem extends ItemGenerico {
	var $tipoDeDato;
	var $valor;
	
	function asHTML() {
		return $valor;
	}
}

class PeriodoDeEvaluacion extends ItemGenerico {
	var $grupoDeEmpleados;
	var $fechaDeInicio;
	var $fechaDeFin;
	var $evaluacionPrototipo;
	
	function initialize() {
		$this->grupoDeEmpleados = new ListaGenerica;
		$this->grupoDeEmpleados->initialize();
	}
	
	function cualitativosOptionMWSList($nombreActual = '') {
		$texto = '';
		$grupo = &$this->evaluacionPrototipo->gruposDeItems->conNombre('objetivosCualitativos');
		
		for ($i = 0; $i < $grupo->count(); $i++) {
			$item = &$grupo->at($i);
			$texto .= '<option value="'.$item->nombre.'"';

			if ($item->nombre == $nombreActual) {
				$texto .= ' selected ';
			}

			$texto .= '>'.$item->descripcion.'</option>';
		}
		return $texto;
	}
	
	function datosExtraDeCualitativos() {
		$grupo = &$this->evaluacionPrototipo->gruposDeItems->conNombre('objetivosCualitativos');
		$texto = 'var thetext1=new Array();';
		$texto .= "thetext1[0]='<font color=red>Debe seleccionar una competencia.</font>';";
		for ($i = 0; $i < $grupo->count(); $i++) {
			$item = &$grupo->at($i);
			$texto .= "thetext1[".($i+1)."]='";
			for ($d = 0; $d < $item->datosExtra->count(); $d++) {
				$datoExtra = &$item->datosExtra->at($d);
				$texto .= $datoExtra->descripcion.": ".$datoExtra->valor;
			}
			$texto .= "<br><br>';";
		}
/*
		$texto .= "thetext1[1]=' Definici�n: Desarrolla talentos individuales al brindar retroalimentaci�n actual y crear planes de desarrollo individual para permitir que las personas alcancen su potencial entero<br><br> Comportamientos anexos: <br><li>Identifica y selecciona a la(s) persona(s) m�s calificada(s) para su(s) puesto(s)</li><li>Participa activamente para crear un desarrollo motivador y realista y planes de carrera para el personal</li><li>Fomenta el que la gente trabaje fuera de su zona de confort y crea las condiciones correctas para que demuestren nuevas habilidades y comportamientos</li><li>Brinda de manera abierta retroalimentaci�n actual constructiva y balanceada respecto al desempe�o</li><li>Funge como gu�a o mentor de confianza</li><br><br> Comportamientos anexos negativos (ejemplos): <br><li>Asigna puestos o roles con base en amistad o para devolver favores, sin determinar a la(s) persona(s) m�s calificada(s) para dicho puesto</li><li>No toma informaci�n sobre los procesos y herramientas de manejo de talentos de Pernod Ricard o ignora los procesos formales en marcha</li><li>No logra hacer del desarrollo una prioridad; no invierte tiempo en ayudar a crear planes de carrera para el personal</li><li>Permanece c�modo con el status quo en lugar de fomentar el que los otros trabajen fuera de su zona de confort</li><li>Incita a otros a tomar riesgos significativos sin ayudarlos a realizar su asesor�a necesaria para planear el apoyo necesario</li><li>No le brinda a los equipos/individuos oportunidades, apoyo y/o recursos amplios</li><li>Retiene o no logra brindar retroalimentaci�n actual del desempe�o</li><li>Enfoca la retroalimentaci�n del desempe�o ya sea en lo negativo o lo positivo sin ofrecer una perspectiva balanceada</li><li>No act�a sobre la retroalimentaci�n proporcionada por otros</li><br><br> Recoomendaciones: <br><li>M�tricas relacionadas que deben considerarse:<li>Tiempo en el trabajo actual</li><li># de reportes directos que han expresado preocupaciones sobre las revisiones de desempe�o (si el individuo es Gerente)</li><li># de reportes directos que reciben capacitaci�n (si el individuo es Gerente)</li><li>Porcentaje de terminaci�n de revisiones anuales de desempe�o y desarrollo (si el individuo es Gerente)</li><li># de reportes directos con planes de desarrollo (si el individuo es Gerente)</li><li>Retroalimentaci�n de clientes internos o externos</li></li><li>Es ambicioso al tomar su experiencia a otro nivel elevando los est�ndares. Si ya es considerado como el mejor en el equipo o departamento, busca ser el mejor en el negocio. Si ya es considerado el mejor en el negocio, buscar ser el mejor en el Grupo</li><li>Solicita reuniones con el Gerente</li><li>Lleva a cabo al menos una reuni�n formal por a�o con los reportes directos para hablar tranquilamente sobre las expectativas y logros respecto al desempe�o y desarrollo</li><li>Le permite a los miembros del equipo que cometan errores y revisa la situaci�n para ayudarlos a concentrarse en hacer lo correcto en la siguiente ocasi�n</li><li>Con base en la evaluaci�n de las fortalezas y necesidades de desarrollo de los miembros del equipo, le ayuda a anticipar los retos que cada uno podr�a enfrentar en sus tareas y debate con ellos el posible camino a seguir</li><li>Brinda una evaluaci�n oportuna y balanceada; recuerda enfatizar primero las fortalezas al discutir las necesidades de desarrollo y determina el mejor momento para darlo</li><li>Utiliza m�ltiples recursos de retroalimentaci�n antes de dar conclusiones sobre el desempe�o de los miembros del equipo</li><li>Incita a los miembros del equipo a ser propietarios de su propio desarrollo. Los dirige hacia los recursos de aprendizaje puestos a disposici�n por la compa��a y da un seguimiento peri�dico para evaluar su progreso</li><br><br>';";
		$texto .= "thetext1[2]=' Definici�n: Crea y dirige equipos de alto desempe�o al fomentar la colaboraci�n y asegurar el cumplimiento de la visi�n compartida<br><br> Comportamientos anexos: <br><li>Inspira a los miembros del equipo al comunicar la visi�n al mismo</li><li>Crea una cultura de rendimiento de cuentas y prop�sito compartido entre los miembros del equipo</li><li>Adapta el estilo de direcci�n a diferentes situaciones y brinda calma y coherencia al equipo durante situaciones de estr�s</li><li>Empodera a los miembros del equipo para tomar decisiones brind�ndoles orientaci�n y apoyo cuando lo requieren</li><li>Eval�a la din�mica y desempe�o del equipo, motiva comportamientos ejemplares</li><li>Entiende las fortalezas y debilidades de los miembros del equipo para facilitar el desempe�o del mismo</li><li>Fomenta el trabajo en equipo y colaboraci�n al promover la apertura y el di�logo</li><br><br> Comportamientos anexos negativos (ejemplos): <br><li>No logra comunicar un regla o prop�sito general del equipo para motivarlo</li><li>Evita tratar asuntos de desempe�o del equipo</li><li>No comparte el poder</li><li>No logar empoderar a los miembros del equipo para tomar decisiones</li><li>Favorece a ciertos miembros del equipo en lugar de tomar decisiones dentro del contexto m�s amplio del equipo completo</li><li>No logra fomentar o esperar trabajo en equipo y colaboraci�n</li><li>No recompensa la colaboraci�n</li><li>Toma cr�dito por el �xito y culpa al equipo por las fallas</li><br><br> Recoomendaciones: <br><li>M�tricas relacionadas que deben considerarse:<li>Terminaci�n de los objetivos del equipo</li><li>Retroalimentaci�n de toda encuesta de compromiso</li><li>Retroalimentaci�n de clientes internos o externos</li></li><li>Muestra entusiasmo por la visi�n del equipo al comunicar lo importante que son las metas del equipo y vincula los esfuerzos de la gente con los objetivos generales</li><li>Identifica los comportamientos que considera que son cr�ticos para el �xito del equipo y despu�s dirige con el ejemplo</li><li>Establece una �identidad del equipo� y trabaja creando orgullo</li><li>Organiza eventos que fomentan el equipo para permitir que la gente se conozca en varios escenarios</li><li>Capitaliza oportunidades para comunicar con frecuencia las prioridades y responsabilidades al equipo</li><li>Se toma el tiempo para orientar a nuevos miembros del equipo en su entorno. Explica claramente las relaciones del equipo, qu� esperar y qui�n es responsable por ello</li><li>Una vez que todos entienden la direcci�n y los roles del equipo, se enfoca en empoderar a los individuos d�ndoles autonom�a en la toma de decisiones y respaldando las decisiones que toman</li><li>Fomenta la cooperaci�n en lugar de la competencia entre diferentes unidades, marcas o regiones</li><li>Toma acciones visibles cuando un miembro del equipo muestra comportamientos individualistas</li><li>Se toma el tiempo para entender los �puntos de vista� de otros, a�n cuando pueden diferir por completo</li><br><br>';";
		$texto .= "thetext1[3]=' Definici�n: Toma iniciativas, pasos audaces y riesgos calculados de forma proactiva para desarrollar el negocio al mismo tiempo que asume responsabilidad para las decisiones<br><br> Comportamientos anexos:<br><li>Continuamente genera nuevas ideas, m�todos, productos y servicios</li><li>Demuestra energ�a y pasi�n al abordar asuntos desafiantes de negocios</li><li>Innova r�pidamente para dar resultados cuando surge nueva informaci�n y cambia prioridades de forma r�pida cuando es necesario</li><li>Reta al �status quo� al pensar fuera del cuadro y toma riesgos con bases</li><li>Muestra la capacidad de mantenerse en situaciones complejas</li><li>Mueve y convence a otros para hacer que las cosas sucedan</li><br><br> Comportamientos anexos negativos (ejemplos):<br><li>Rechaza ideas innovadoras presentadas por otros (por ejemplo, muestra falta de respeto hacia otras opiniones o puntos de vista)</li><li>Al tomar en cuenta opciones, con frecuencia se basa en m�todos establecidos en lugar de explorar nuevos enfoques</li><li>No logra reconocer el valor de la innovaci�n (por ejemplo, se enfoca en por qu� la ideas �no funcionar�n� o �tomar�n demasiado tiempo�) </li><li>Rechaza las iniciativas creativas de otros (por ejemplo, no motiva a los equipos, no fomenta nuevos enfoques)</li><li>No act�a en situaciones complejas; participa mayormente en actividades que son ya familiares y que garantizan ser exitosas</li><li>Se enfoca en evitar errores en lugar de tomar riesgos informados</li><li>No logra tomar decisiones oportunas (por ejemplo, no actuar� hasta que la informaci�n completa est� disponible, permite que situaciones ambiguas contin�en)</li><li>Culpa a los dem�s cuando se enfrenta al fracaso</li><br><br> Recoomendaciones: <br><li>M�tricas relacionadas que deben considerarse:	<li>Contribuci�n a un Nuevo Producto/Desarrollo de Negocio o Campa�a de Mercadotecnia</li>	<li>Compromiso de otros para apoyar una nueva idea o causa</li>	<li>Retroalimentaci�n de clientes internos o externos</li></li><li>Inicia 2 o 3 sesiones dedicadas a crear una lluvia de ideas respecto a un asunto o pregunta en particular cuando surge una oportunidad estrat�gica </li><li>Durante el proceso creativo, suspende las declaraciones cr�ticas que dicen �no funcionar� y por el contrario piensa en t�rminos positivos como �puede funcionar porque��</li><li>Genera tantas ideas como sea posible durante una sesi�n de lluvia de ideas; si no hay m�s ideas, toma un descanso y regresa despu�s para redefinir el problema y verlo desde una perspectiva diferente</li><li>Recompensa a los empleados por sus buenas ideas al agradecerles y decirles a los dem�s sobre sus buenas ideas</li><li>Pide retroalimentaci�n a colegas en los que conf�a sobre situaciones en las que hay tendencia a tener demasiadas opiniones o ser muy inflexible</li><li>Con frecuencia confronta nuevas ideas y tendencias; crea curiosidad intelectual al leer los peri�dicos y publicaciones para enterarse sobre eventos actuales o nuevos desarrollos de negocio</li><li>Ayuda a poner en marcha ideas y cuando se cometen errores se enfoca en la organizaci�n y aprende de ellos</li><li>Eval�a sus propias reacciones ante cambios pasados para evaluar lo que ocasion� la resistencia y c�mo lo super�. Se re�ne con otros para tratar miedos y preocupaciones</li><br><br>';";
		$texto .= "thetext1[4]=' Definici�n: Brinda resultados y empodera a los dem�s al establecer objetivos claros, proporcionar los recursos y la retroalimentaci�n apropiada y asegurar enfocarse en alcanzar los resultados<br><br> Comportamientos anexos<br><li>Logra sus objetivos individuales en su propio alcance de trabajo al aplicar est�ndares profesionales de excelencia</li><li>Toma en cuenta las mejores pr�cticas y experiencias pasadas para lograr un trabajo de alta calidad</li><li>Muestra un sentido de urgencia para cumplir metas y toma acciones correctivas para asegurar resultados</li><li>Asigna tareas y responsabilidades para resultados de trabajo a los individuos m�s adecuados, cuando es necesario</li><li>Mantiene la calma y altos est�ndares de desempe�o en ambientes desafiantes</li><li>Mide y da seguimiento a los resultados y procesos clave del negocio para evaluar el desempe�o e identificar mejoras</li><br><br> Comportamientos anexos negativos (ejemplos): <br><li>No logra entender los puntos clave de su propio desempe�o en el trabajo actual</li><li>Considera la direcci�n del desempe�o como una actividad que se realiza una vez por a�o</li><li>No demuestra una actitud de servicio al cliente cuando interact�a con otros</li><li>Evita tratar aspectos de desempe�o</li><li>Se basa en enfoques actuales u obvios en lugar de considerar las mejores pr�cticas o aprender de experiencias pasadas</li><li>Asigna tareas y responsabilidades sin considerar a qui�n ser�a m�s conveniente asignarlas</li><li>Muestra un sentido de estr�s, p�nico o frustraci�n en momentos de desaf�o y puede perder en enfoque en los objetivos</li><br><br> Recoomendaciones: <br><li>M�tricas relacionadas que deben considerarse:<li>Clasificaci�n general</li><li>Contribuci�n a los resultados del Grupo</li><li>Porcentaje de proyectos completados a tiempo y dentro del presupuesto</li><li>Porcentaje de revisi�n de desempe�o y desarrollo anual alcanzado</li><li>Porcentaje de terminaci�n de plan anual de capacitaci�n</li><li>Retroalimentaci�n de clientes internos o externos</li></li><li>Solicita retroalimentaci�n frecuente del Gerente con relaci�n a las expectativas de desempe�o y brechas por cubrir</li><li>Es persistente cuando confronta contratiempos. Consulta a otros para obtener nuevos insights para abordar asuntos</li><li>Para maximizar el desarrollo personal, busca tareas demandantes que le permitir�n sacar provecho de sus fortalezas y mejorar sus debilidades</li><li>Busca tareas en las que sea posible aprender de una persona con m�s experiencia o un experto reconocido para obtener conocimiento y experiencia espec�fica</li><li>Controla el comportamiento al abstenerse de expresar p�nico, llanto o mostrar temor. Mantiene la comunicaci�n racional y objetiva y brinda direcci�n a otros si es necesario</li><li>Revisa las medidas de desempe�o en marcha para el equipo. Considera los cambios que se han puesto en marcha en el negocio e identifica lo que debe revisarse, eliminarse o crearse.</li><br><br>';";
		$texto .= "thetext1[5]=' Definici�n: Define la visi�n del futuro al identificar oportunidades para crear valor o mejor en la direcci�n a largo plazo y comparte la visi�n de forma convincente para inspirar un cambio.<br><br> Comportamientos anexos:<br><li> Anticipa cambios en el ambiente interno o externo para crear una visi�n del futuro para el negocio</li><li> Crea y mantiene relaciones estrat�gicas (intra-Grupo, clientes, gobierno, socios, grupos de industrias, etc.)</li><li> Demuestra una comprensi�n de las conexiones entre las �reas del negocio e incorpora esta perspectiva en las decisiones</li><li> Eval�a el nivel de cambio requerido para alcanzar la visi�n y desarrolla planes para apoyar la transici�n</li><li> Traduce la visi�n de la organizaci�n en objetivos claros, espec�ficos y alcanzables</li><li> Comunica las necesidades y beneficios de cambio del negocio para fomentar el compromiso</li><li> Identifica criterios para evaluar la alineaci�n estrat�gica de los planes con base en los factores de �xito para el negocio que son susceptibles de ser medidos</li><br><br> Comportamientos anexos negativos (ejemplos)<br><li>Muestra poco entusiasmo por la visi�n de Pernod Ricard y no logra promover activamente la visi�n y objetivo de Pernod Ricard a los empleados (por ejemplo, no organiza reuniones para transmitir en cascada la informaci�n clave, se comunica sin convicci�n en eventos) </li><li>No traduce la visi�n de Pernod Ricard en un plan estrat�gico para el �rea de responsabilidad asignada</li><li>Con frecuencia da respuestas inadecuadas a las preguntas de los empleados con respecto a la visi�n y direcci�n de la organizaci�n y las conexiones entre las �reas del negocio (por ejemplo, no se identifica con las decisiones de las altas direcciones e incluso expresa puntos de vista en foros p�blicos que contradicen a aqu�llos del gerente senior, no aclara los roles dentro de la organizaci�n o la importancia de cada empleado)</li><li>No comunica objetivos espec�ficos para apoyar la ejecuci�n de la estrategia y no muestra inter�s al medir el progreso en el camino para motivar y/o adaptar</li><br><br> Recoomendaciones: <br><li>M�tricas relacionadas que deben considerarse:<li>Contribuci�n a la rentabilidad del grupo</li><li>Participaci�n en Planes de Negocio y presentaci�n de los mismos</li><li>Visi�n del futuro claramente definida para el Departamento/Negocio/�rea</li><li>Retroalimentaci�n de clientes internos o externos</li></li><li>Comunica claramente los planes estrat�gicos y objetivos de soporte de la Regi�n/Marca/Filial y explica c�mo contribuye al plan estrat�gico de Pernod Ricard durante reuniones formales e informales</li><li>Proporciona a los empleados una copia de la visi�n y metas estrat�gicas de Pernod Ricard y se asegura de que su rol para alcanzarlas est� claramente explicado, especialmente en la Revisiones Anuales</li><li>Cuando se debaten las iniciativas o prioridades con los empleados, menciona c�mo �stas est�n relacionadas con la misi�n, visi�n y valores de Pernod Ricard</li><li>Mantiene a los empleados informados sobre el progreso de la Regi�n/Marca/Filial para alcanzar su misi�n y visi�n; incluye datos concretos (por ejemplo, cifras en euros, n�meros de producci�n y ventas) como soporte a sus comentarios</li><li>Involucra a los miembros del equipo durante tiempos de cambio y los gu�a, a�n m�s, cuando la decisi�n del cambio no es una decisi�n compartida</li><li>Crea iniciativas de cambio para que otros quieran ser parte del mismo. Les permite a los dem�s participar y les da la autoridad y control para tomar decisiones. Comparte el poder</li><li>Contribuye con peque�os triunfos al contar historias con frecuencia sobre el progreso hacia el progreso y visi�n de la organizaci�n</li><li>Comparte los estatutos de Pernod Ricard dentro de la Regi�n/Marca/Filial y brinda ejemplos espec�ficos para ilustrar sus diferentes componentes a los empleados</li><li>Dedica una parte significativa del tiempo de una reuni�n en componentes clave de la Regi�n/Marca/Filial para obtener retroalimentaci�n sobre nuevos productos/servicios, mejora de calidad, etc.</li><li>Identifica oportunidades estrat�gicas en el mercado y permite a un equipo de Talentos hacer cualquier recomendaci�n necesaria, etc.</li><li>Fomenta que los empleados identifiquen las mejores y pr�ximas pr�cticas para Pernod Ricard o la Regi�n/Marca/Filial</li><br><br>';";    
		$texto .= "thetext1[6]=' Definici�n: Representa y comunica de manera entusiasta los valores clave de Pernod Ricard, apeg�ndose a la �tica y el fuerte compromiso con las iniciativas de CSR<br><br> Comportamientos anexos:<br><li>Mantiene altos est�ndares profesionales que est�n alineados con los valores, �tica y los estatutos de la organizaci�n</li><li>Funge como rol modelo y traduce los valores en comportamientos comprensibles para otros</li><li>Promueve la �tica al confrontar y tratar comportamientos inapropiados y no �ticos</li><li>Demuestra un compromiso con las prioridades de Responsabilidad Social Corporativa (CSR) al fomentar iniciativas de Grupo y locales</li><li>Establece un ambiente mutuo de confianza al comunicarse de forma honesta, directa y transparente con colegas de todos los niveles</li><li>Celebra el �xito y reconoce la contribuci�n de otros</li><br><br> Comportamientos anexos negativos (ejemplos): <br><li>Aplica est�ndares profesionales cuestionables que pueden no estar en l�nea con los valores, �tica y estatutos de la organizaci�n</li><li>Ignora o no logra lidiar con los comportamientos no �ticos de otros</li><li>No logra demostrar compromiso con las prioridades de Responsabilidad Social Corporativa (CSR)</li><li>Retiene informaci�n; comunica solo informaci�n parcial y evita la publicaci�n completa</li><li>Conserva todos los cr�ditos para s� mismo en lugar de reconocer las contribuciones del equipo</li><br><br> Recoomendaciones: <br><li>M�tricas relacionadas que deben considerarse al evaluar esta competencia(si aplica):<li>Participaci�n en las iniciativas de Responsabilidad Social Corporativa (CSR)</li><li>Retroalimentaci�n de clientes internos o externos</li></li><li>Mantiene sus promesas despu�s de hacerlas, sin importar qu� tan significativas son</li><li>Lee los estatutos de Pernod Ricard y hace suyos sus valores y �tica. Se las explica a otros durante reuniones formales e informales</li><li>Cuando se comete un error, corrige el error tan pronto como sea posible. Reporta el error para determinar si �ste ha tenido un impacto en otros y dichas personas requieren ayuda para lidiar con las consecuencias</li><li>Se comunica directamente con alguien si dicha persona est� haciendo algo que se considera inadecuado o no �tico. Explica que se tomar�n acciones si no detiene o rectifica la situaci�n</li><li>Evita toda actividad �ticamente cuestionable. Si no est� seguro, busca asistencia de otros</li><li>Solicita al departamento de RH casos de estudio sobre toma de decisiones �ticas y pide que sean presentados al equipo</li><li>Se fija como meta el reconocer y recompensar a alguien durante la pr�xima reuni�n de equipo</li><li>Participa en las iniciativas de CSR y propone nuevas iniciativas</li><br><br>';";
*/		
		return $texto;
	}
	
}

class EvaluacionDeEmpleado extends ItemGenerico {
	var $evaluado;
	var $evaluador;
	var $reviewer;
	var $fechaDeCreacion;
	var $cantidadDeObjetivosCuantitativosRequeridos;
	var $cantidadDeObjetivosCualitativosRequeridos;
	var $items;
	var $gruposDeItems;
	
	function initialize() {
		$this->nombre = 'Evaluacion nueva';
		$this->items = new ListaGenerica;
		$this->items->initialize();
		$this->gruposDeItems = new ListaGenerica;
		$this->gruposDeItems->initialize();
	}
	
	function asString() {
		$texto = "evaluacionDeEmpleado id[".$this->id."] de: ".$this->evaluado->nombreCompleto()." evaluador: ".$this->evaluador->nombreCompleto()." reviewer: ".$this->reviewer->nombreCompleto()."<BR>
		Objetivos requeridos:<BR>
		Cuantitativos : ".$this->cantidadDeObjetivosCuantitativosRequeridos." / 
		Cualitativos  : ".$this->cantidadDeObjetivosCualitativosRequeridos."<BR><BR>";
 	  for ($i = 0; $i < $this->gruposDeItems->count(); $i++) {
 	  	$grupo = $this->gruposDeItems->at($i);
			$texto .= $grupo->asHTMLString()."<BR>";
 	  }
 	  return $texto;
	}
	
	function addGrupo(&$grupo) {
		$this->gruposDeItems->add($grupo);
	}

	function addItem(&$item, &$grupo = null) {
		$this->items->add($item);
		if (isset($grupo)) {
			if ($grupo->nombre <> $item->nombre) {
				$grupo->add($item);
			}
		}
	}
	
	function removeItem(&$item) {
		$grupo = &$this->grupoDeItem($item);
		if (isset($grupo)) {
			$grupo->remove($item);
		}
		$this->items->remove($item);
	}
	
	function objetivoCuantitativoNumero($numeroDeOrden) {
		return $this->gruposDeItems->conNombre('objetivosCuantitativos')->at($numeroDeOrden);
	}

	function objetivoCualitativoNumero($numeroDeOrden) {
		return $this->gruposDeItems->conNombre('objetivosCualitativos')->at($numeroDeOrden);
	}
	
	function itemVistoPorEvaluador(&$item) {
		return ($item->estado->nombre == 'aprobadoPorEvaluador') || ($item->estado->nombre == 'aprobadoPorReviewer') || ($item->estado->nombre == 'rechazadoPorReviewer');
	}
	
	function itemVistoPorReviewer(&$item) {
		return ($item->estado->nombre == 'aprobadoPorReviewer');
	}
	
	function itemModificable(&$item) {
		return $item->estado->nombre == 'abiertoParaModificacion';
	}
	
	function objetivoCuantitativoNuevo(&$sesionDmasD) {
		$objetivo = new ItemDeEvaluacion;
		$objetivo->initialize();
		$objetivo->initializeDatosExtra('objetivoCuantitativo');
		$objetivo->estado = &$sesionDmasD->estadosDeItems->conNombre('abiertoParaModificacion');
		$objetivo->id = -1;
		$grupo = &$this->gruposDeItems->conNombre('objetivosCuantitativos');
		$objetivo->nombre = 'objetivoCuantitativo'.($grupo->count()+1);
		$this->addItem($objetivo, $grupo);
		return $objetivo;
	}

	function objetivoCualitativoNuevo(&$sesionDmasD) {
		$objetivo = new ItemDeEvaluacion;
		$objetivo->initialize();
		$objetivo->initializeDatosExtra('objetivoCualitativo');
		$objetivo->estado = $sesionDmasD->estadosDeItems->conNombre('abiertoParaModificacion');
		$objetivo->id = -1;
		$grupo = &$this->gruposDeItems->conNombre('objetivosCualitativos');
		$objetivo->nombre = 'objetivoCualitativo0';
		$this->addItem($objetivo, $grupo);
		/* Se crean las 3 acciones correspondientes a experienciaEnElPuesto, coach, cursosYLecturas */

		$experiencia = new ItemDeEvaluacion;
		$experiencia->id = -1;
		$objetivo->addAccion($experiencia);
		$experiencia->estado = $sesionDmasD->estadosDeItems->conID(1);
//		guardarAccion($sesionDmasD, $this, $objetivo, $experiencia);
		
		$coach = new ItemDeEvaluacion;
		$coach->id = -1;
		$objetivo->addAccion($coach);
		$coach->estado = $sesionDmasD->estadosDeItems->conID(1);
//		guardarAccion($sesionDmasD, $this, $objetivo, $coach);
		
		$cursos = new ItemDeEvaluacion;
		$cursos->id = -1;
		$objetivo->addAccion($cursos);
		$cursos->estado = $sesionDmasD->estadosDeItems->conID(1);
//		guardarAccion($sesionDmasD, $this, $objetivo, $cursos);
		
		return $objetivo;
	}

	function ponderacionDisponiblePara(&$item) {
		$ponderacionRestante = 100;
		$grupo = &$this->grupoDeItem($item);
		for ($nroItem = 0; $nroItem < $grupo->count(); $nroItem++) {
			$otroItem = &$grupo->at($nroItem);
			$ponderacionRestante = $ponderacionRestante - $otroItem->ponderacion;
		}
		if (isset($item->ponderacion)) {
			$ponderacionRestante = $ponderacionRestante + $item->ponderacion;
		}
		return $ponderacionRestante;
	}

	function grupoDeItem(&$item) {
		for ($nroGrupo = 0; $nroGrupo < $this->gruposDeItems->count(); $nroGrupo++) {
			$grupo = &$this->gruposDeItems->at($nroGrupo);
			if ($grupo->includes($item)) {
				return $grupo;
			}
		}
	}

}

class EstadoDeItem extends ItemGenerico {
	var $tipoDeDato;
}

class Empleado extends ItemGenerico {
	var $numeroDeLegajo;
	var $fechaDeIngreso;
	var $urlDeFoto;
	var $nombres;
	var $apellidos;
	var $direccion;
	var $telefonoFijo;
	var $telefonoMovil;
	var $planta;
	var $area;
	var $posicion;
	var $plandecarrera;
	var $nombreDeConvenio;
	var $email;
	var $esEvaluador;
	var $esReviewer;
	var $grupos;
	
	function nombreCompleto() {
		return $this->apellidos.', '.$this->nombres;
	}
	
	function initialize() {
		$this->grupos = new ListaGenerica;
	}
	
}

class Mensaje extends ItemGenerico {
	var $fechaHora;
	var $fechaHoraDeLeido;
	var $usuario;
	var $item;
	var $evaluacion;
	var $destinatario;

	function textoCorto() {
		$texto = substr($this->descripcion,1,40);
		return $texto;
	}
	
	function leido() {
		return !is_null($this->fechaHoraDeLeido);
	}
}

class SesionDmasD {
	var $usuarios;
	var $usuario;
	var $usuarioDelSistema;
	var $empleadoEnRevision;
	var $periodoDeEvaluacion;
	var $evaluacionDeEmpleado;
	var $evaluacionEnRevision;
	var $notificaciones;
	var $mensajes;
	var $valoresPredefinidos;
	var $estadosDeItems;
	
	function initialize() {
		$this->usuario = new Usuario;
		$this->usuarios = new ListaGenerica;
		$this->usuarios->initialize();
		$this->notificaciones = new ListaGenerica;
		$this->notificaciones->initialize();
		$this->mensajes = new ListaGenerica;
		$this->mensajes->initialize();
		$this->valoresPredefinidos = new ListaGenerica;
		$this->valoresPredefinidos->initialize();
		$this->estadosDeItems = new ListaGenerica;
		$this->estadosDeItems->initialize();
		$this->empleadoEnRevision = null;
		$this->periodoDeEvaluacion = null;
		$this->evaluacionDeEmpleado = null;
		$this->usuarios->add($this->usuario);
	}
	
	function empleadoConID($idEmpleado) {
		// devuelvo el empleado que forma parte de la lista de usuarios
		// si no existe, lo busco y lo cargo
		// si sigue sin existir, devuelvo null
		for ($i = 0; $i < $this->usuarios->count(); $i++) {
			$usuario = &$this->usuarios->at($i);
			if (isset($usuario->empleado)) {
				$empleado = &$usuario->empleado;
				if ($empleado->id == $idEmpleado) {
					return $empleado;
				}
			}
		}
		$usuario = usuarioConIDEmpleadoFromDB($idEmpleado);
		if (isset($usuario)) {
			$this->usuarios->add($usuario);
			return $usuario->empleado;
		} else {
			return null;
		}
	}
	
	function usuarioDelEmpleado(&$empleado) {
		for ($i = 0; $i < $this->usuarios->count(); $i++) {
			$usuario = $this->usuarios->at($i);
			if ($usuario->empleado->id == $empleado->id) {
				return $usuario;
			}
		}
		$usuario = usuarioConIDEmpleadoFromDB($empleado->id);
		$usuario->empleado = $empleado;
		return $usuario;
	}
	
	function hayNotificacionesNuevas() {
		for ($i = 0; $i < $this->notificaciones->count(); $i++) {
			$notificacion = &$this->notificaciones->at($i);
			if (!$notificacion->leido()) {
				return true;
			}
		}
		return false;
	}
	
	function notificacionesMWSList() {
	$texto = '<ul class="mws-notifications">';
	for ($i = 0; $i < $this->notificaciones->count(); $i++) {
		$notificacion = $this->notificaciones->at($i);
		if ($notificacion->leido()) {
			$leido = 'read';
		} else {
			$leido = 'unread';
		}
		$texto .= '<li class="'.$leido.'"><a href="#"><span class="message">';
		$texto .= $notificacion->descripcion;
		$texto .= '</span><span class="time"></span></a></li>';
	}
	$texto .= '</ul>';
	return $texto;
	}
	
	function limpiarMensajes() {
		$this->notificaciones->initialize();
		if (isset($this->evaluacionEnRevision)) {
			for ($i = 0; $i < $this->evaluacionEnRevision->items->count(); $i++) {
				$item = &$this->evaluacionEnRevision->items->at($i);
				$item->comentarios->initialize();
			}
		}
	}

	function aplicarEstadosSobreItemsDeEvaluacion(&$evaluacion) {
		// Recorremos los items de la evaluacion
		for ($nroDeItem = 0; $nroDeItem < $evaluacion->items->count(); $nroDeItem++) {
			$item = &$evaluacion->items->at($nroDeItem);
			if (isset($item->estado)) {
				$item->estado = $this->estadosDeItems->conID($item->estado);
			}
			// Recorremos las acciones del item
			for ($nroAccion = 0; $nroAccion < $item->acciones->count(); $nroAccion++) {
				$accion = &$item->acciones->at($nroAccion);
				if (isset($accion->estado)) {
					$accion->estado = $this->estadosDeItems->conID($accion->estado);
				}
			}
			
		}
	}
	
	function estadosOptionMWSList($tipoDeDato, $idEstadoSeleccionado = -1) {
		$texto = '';
		for ($i = 0; $i < $this->estadosDeItems->count(); $i++) {
			$estado = &$this->estadosDeItems->at($i);
			if ($estado->tipoDeDato == $tipoDeDato) {
				$texto .= '<option value="'.$estado->id.'"';
				if ($estado->id == $idEstadoSeleccionado) {
					$texto .= ' selected ';
				}
				$texto .= '>'.$estado->descripcion.'</option>';
			}
		}
		return $texto;
	}
	
	function notificacionesParaMensaje(&$mensaje) {
		$notificaciones = new ListaGenerica;
		$notificaciones->initialize();

		if (($mensaje->usuario->id <> 0) && (isset($mensaje->item))) {
			// es un comentario sobre un item ($item <> null && $usuario <> 0)
			// se notifica a todos los involucrados en la evaluacion, excepto quien hizo el comentario
			$texto = $mensaje->usuario->nombreCompleto()." ha creado un nuevo comentario para el item ".$mensaje->item->descripcion;
			$usuario = &$this->usuarioDelSistema;
			$fechaHora = date('Y-m-d h:i:s');
			$notificacion1 = new Mensaje;
			$notificacion2 = new Mensaje;
			$notificacion1->usuario = &$usuario;
			$notificacion2->usuario = &$usuario;
			$notificacion1->descripcion = $texto;
			$notificacion2->descripcion = $texto;
			$notificacion1->item = &$mensaje->item;
			$notificacion2->item = &$mensaje->item;
			$notificacion1->fechaHora = $fechaHora;
			$notificacion2->fechaHora = $fechaHora;
			if ($mensaje->usuario->empleado->id == $mensaje->evaluacion->evaluado->id) {
				$notificacion1->destinatario = $this->usuarioDelEmpleado($mensaje->evaluacion->evaluador);
				$notificacion2->destinatario = $this->usuarioDelEmpleado($mensaje->evaluacion->reviewer);
			}
			if ($mensaje->usuario->empleado->id == $mensaje->evaluacion->evaluador->id) {
				$notificacion1->destinatario = $this->usuarioDelEmpleado($mensaje->evaluacion->evaluado);
				$notificacion2->destinatario = $this->usuarioDelEmpleado($mensaje->evaluacion->reviewer);
			}
			if ($mensaje->usuario->empleado->id == $mensaje->evaluacion->reviewer->id) {
				$notificacion1->destinatario = $this->usuarioDelEmpleado($mensaje->evaluacion->evaluado);
				$notificacion2->destinatario = $this->usuarioDelEmpleado($mensaje->evaluacion->evaluador);
			}
			$notificaciones->add($notificacion1);
			$notificaciones->add($notificacion2);
		}
		return $notificaciones;
	}

}


?>
