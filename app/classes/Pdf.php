<?php

require_once 'app/fpdf/fpdf.php';

class Pdf{



    public function generatePdf_forOrders($order_id,$first_name,$last_name,$address,$state,$email,$niz,$totalPrice,$filename){
        $pdf = new FPDF();


        $pdf->AddPage();
        
       
        $pdf->SetFont('Arial','B',12);
     
        $pdf->Cell(0,10,'#'. $order_id.' order',0,1,'C');
     
        $pdf->Cell(0,0,'','B',1,'C');
     
        $pdf->Ln(10);
        
  
        $pdf->SetFont('Arial','',10);
        

        $pdf->Cell(0,10,'Name: '.$first_name.' '.$last_name,0,1);
        $pdf->Cell(0,10,'Address: '. $address,0,1);
        $pdf->Cell(0,10,'State: '.$state,0,1);
        $pdf->Cell(0,10,'Email: '.$email,0,1);
        
      
        
        //$pdf->Image('image/image.jpg',10,$pdf->GetY(),30);
        $pdf->Ln(10);

        foreach ($niz as $value){

        $pdf->Cell(0,0,'','T',1,'C');
        $pdf->Cell(0,10,'Name of artical: '.$value['name'],0,1);
        $pdf->Cell(0,10,'Price: '.$value['price'] .'$',0,1);
        $pdf->Cell(0,10,'Amount: '.$value['quantity'],0,1);
        $pdf->Ln(10);
}

        $pdf->Ln(10);
        $pdf->Cell(0,10,'Total price: '.$totalPrice .'$',0,1);
        
    
                    
 
        $pdf->Output('F', $filename);
          
    }
}