<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Illuminate\Support\Facades\Storage;

class PrintController extends Controller
{
   
    public function printReceipt(Request $request)
    {

        try {
            // Obtener los datos del JSON
            $data = $request->json()->all();
            // Obtener los campos del JSON
            $nombre = $data['nombre'];
            $direccion = $data['direccion'];
            $cliente = $data['cliente'];
            $idPedido = $data['idPedido'];
            $cantidad = $data['cantidad'];
            $numImpresiones = $cantidad;

            for ($i = 0; $i < $numImpresiones; $i++) {
                $profile = CapabilityProfile::load("SP2000");
                $connector = new WindowsPrintConnector("smb://192.168.139.1/ZDesigner ZD230-203dpi ZPL");
                $printer = new Printer($connector, $profile);
    
                $printer = new Printer($connector);
    
                $archivoZpl = 'public/lbl/label.zpl'; // Ruta relativa al archivo dentro de storage/app/public
                $rutaArchivoZpl = Storage::path($archivoZpl);
                $contenidoZpl = file_get_contents($rutaArchivoZpl);
                $contenidoZpl=str_replace('[[PARA]]',$cliente,$contenidoZpl);
                $contenidoZpl=str_replace('[[DE]]',$nombre,$contenidoZpl);
                $contenidoZpl=str_replace('[[DIRECCION]]',$direccion,$contenidoZpl);

                $numImpresiones = $cantidad; // Número de veces que se desea imprimir

                    $printer->textRaw($contenidoZpl);
                    //$printer->textRaw("^XZ"); 
                
                $printer->close();
                
            }
            return response()->json(['correctProcess'=>true,'message' => 'Impresión realizada correctamente']);
        } catch (\Throwable $th) {
            return response()->json(['correctProcess'=>false,'message' => $th->getMessage()]);
        }
    }

}
