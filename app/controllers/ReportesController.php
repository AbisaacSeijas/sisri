<?php

class ReportesController extends BaseController {

	public function registros()
	{
		$data['registros'] = Persona::with(array(
			'direccion.zona',
			'telefonos',
			'visitas',
			'visitas.publicador'
		))->join('direcciones', 'personas.direcciones_id', '=','direcciones.id')
		->orderBy('direcciones.calle_avenida', 'DESC')
		->whereIn('personas.id', array(390,391,158,159,171/*,160,161,162,163,164,220,227,228,144,145,146,149,150,151,152,153,154,155,156,157,208,209,210,211,212,213,214,140,142,143,176,177,178,179,180,181,182,221,222,223,224,225,226*/))
		->get();

		#echo "<pre>";print_r($data['registros']->toArray()); exit;

		$pdf = PDF::loadView('pdf.registros', $data);

		#var_dump($data);

		#return View::make('pdf.registros', $data); 

		return $pdf->stream();
	}

	public function persona($id){
		$data['registros'] = Persona::with(array(
			'direccion.zona',
			'telefonos',
			'visitas',
			'visitas.publicador'
		))->find(array($id))/*find(array(125,150))*/;

		#echo "<pre>";print_r($data['registros']->toArray()); exit;

		$pdf = PDF::loadView('pdf.registros', $data);

		#var_dump($data);

		#return View::make('pdf.registros', $data); 

		return $pdf->stream();
	}

	public function excel(){

		$registros = Persona::with(array(
			'direccion.zona',
			'telefonos',
			'visitas',
			'visitas.publicador'
		))
		->select(array('personas.*', DB::raw('personas.id as id_persona')))
		->join('direcciones', 'personas.direcciones_id', '=','direcciones.id')
		->join('zonas', 'direcciones.zonas_id', '=', 'zonas.id')
		->orderBy('direcciones.calle_avenida', 'DESC')
		->where('zonas.zona', 'Los Teques')
		->where('tipo_reg', 'casa')
		->where('personas.id', '<', 393)
		->get();

		#$str = View::make('reportes.excel', $data)->render();

		

		$table="<table><thead>
				<tr>
					<th>id</th>
					<th>Nombre</th>
					<th>Proveniente</th>
					<th>Genero</th>
					<th>Edad</th>
					<th>Direccion</th>
				</tr></thead><tbody>";
				foreach ($registros as $registro){
		$table.="<tr>
					<td>{$registro->id}</td>
					<td>{$registro->nombre} {$registro->apellido}</td>
					<td>{$registro->proveniente}</td>
					<td>{$registro->genero}</td>
					<td>{$registro->edad}</td>
					<td>" . Direccion::getStringDireccion($registro->direcciones_id) . "</td>
					
				</tr>";
				}
		$table.="</tbody></table>";

		if (mb_detect_encoding($table) == 'UTF-8') {
		   $table = mb_convert_encoding($table , "HTML-ENTITIES", "UTF-8");
		}


		$headers = array(
	        'Pragma' => 'public',
	        'Expires' => 'public',
	        'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
	        'Cache-Control' => 'private',
	        'Content-Type' => 'application/vnd.ms-excel',
	        'Content-Disposition' => 'attachment; filename=registros.xls',
	        'Content-Transfer-Encoding' => ' binary'
	    );
	    return Response::make($table, 200, $headers);

		#return View::make('reportes.excel', $data);

	}

}