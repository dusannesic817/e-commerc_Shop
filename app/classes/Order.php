<?php 


class Order extends Cart{

    protected $conn;
    protected $notification;
    protected $mailer;
    protected $pdf;

    public function __construct(Notification $notification, Mailer $mailer, Pdf $pdf){
        global $conn;  
        $this->conn =$conn;
        $this->notification=$notification;
        $this->mailer=$mailer;
        $this->pdf=$pdf;
    }


    public function create_order($first_name, $last_name, $email, $adress,$country,$number){

       // return $this->get_cart_items();
       $cart_items=$this->get_cart_items();


      $sql="INSERT INTO `delivery_adreses` (`first_name`,`last_name`,`email`,`adress`,`country`,`number`)
        VALUES
         (?,?,?,?,?,?);
      
     " ;

     $stmt=$this->conn->prepare($sql);
     $stmt->bind_param("ssssss",$first_name, $last_name, $email, $adress,$country,$number);
      $stmt->execute();


      $delivery_adress_id=$this->conn->insert_id;


      $sqlo="INSERT INTO `orders`(`user_id`,`delivery_adress_id`)
      values(?,?)
      ";

      $stmt->prepare($sqlo);
      $stmt->bind_param("ii",$_SESSION["id"], $delivery_adress_id);
      $stmt->execute();

      $order_id=$this->conn->insert_id;
      $_SESSION['last_order_id']=$order_id;

     

      $sqli="INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`)
      VALUES (?,?,?)
      
      ";
      $stmt=$this->conn->prepare($sqli);
      foreach($cart_items as $value){
        $stmt->bind_param("iis",$order_id, $value["id"],$value["quantity"] );
        $result=$stmt->execute();
      }

      
      $list_order=$this->list_order();
      $totalPrice=0;
      $array=[];
      foreach($list_order as $value){

       $article=$value['name'];
       $price=$value['price'];
       $amount=$value['quantity'];

       $array[]=[
          'name'=> $article,
          'price' => $price,
          'quantity'=> $amount

       ];


       
       $total = $price * $amount;
       $totalPrice += $total;

      }

      if($result){
        $filename = "public/pdf_files/order_number" . $order_id. ".pdf";

        $update=$this->update_order($filename,$order_id);

        if($update){
          $this->notification->notification($filename);
        }


        $this->pdf->generatePdf_forOrders($order_id,$first_name,$last_name,$adress,$country,$email,$array,$totalPrice,$filename);
        $this->mailer->sendMailReportPdf($filename,$email);

      }

      
     

    }


    public function empty_order(){
      $id=$_SESSION["id"];
      $sql="DELETE FROM `cart`WHERE `user_id`=$id
      ";
      $stmt=$this->conn->prepare($sql);
      $stmt->prepare($sql);
      return $stmt->execute();
    }


    public function empty_cart(){
      $id=$_SESSION["id"];
      $sql="DELETE FROM `cart`WHERE `user_id`=$id
      ";
      $stmt=$this->conn->prepare($sql);
      $stmt->prepare($sql);
      return $stmt->execute();
    }


    public function list_order(){
      $sql="SELECT 
      `orders`.id as order_id,
      `products`.name as name,
      `products`.`image` as image,
      clubs.name as club_name,
      products. price as price,
      order_items.quantity as quantity,
      orders.created_at as created_at,
      delivery_adreses.adress as address,
      delivery_adreses.country as country,
      delivery_adreses.first_name as first_name,
      delivery_adreses.last_name as last_name,
      delivery_adreses.email as email,
      delivery_adreses.number as number
      FROM order_items
      LEFT JOIN orders ON order_items.order_id = orders.id
      LEFT JOIN products on order_items.product_id= products.id
      LEFT JOIN delivery_adreses on delivery_adreses.id = orders.delivery_adress_id
      LEFT JOIN clubs on products.club_id= clubs.id
      WHERE orders.user_id= ?
      AND order_items.order_id=?";

      $stmt=$this->conn->prepare($sql);
      $stmt->bind_param("ii", $_SESSION["id"],$_SESSION['last_order_id']);
      $stmt->execute();
      $result= $stmt->get_result();


      if($result->num_rows>0){

        return $result->fetch_all(MYSQLI_ASSOC);
    }


  }

  public function update_order($path, $id) {
    $sql= 'UPDATE orders SET pdf_file_path = ? WHERE id = ?';
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param('si', $path, $id);
    $result = $stmt->execute();
    
   
    if ($result) {
        return true;
    } else {
        return false;
    }
}


    

 


    

}