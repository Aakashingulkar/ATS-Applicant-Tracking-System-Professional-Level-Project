<?php
include("../config.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../fpdf/fpdf.php';
require '../src/Exception.php';
require '../src/PHPMailer.php';
require '../src/SMTP.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../login.php");
    exit();
}

$app_id = intval($_GET['id']);

$query = mysqli_query($conn,"
SELECT a.*, u.name, u.email, j.title, j.location
FROM applications a
JOIN users u ON a.candidate_id = u.id
JOIN jobs j ON a.job_id = j.id
WHERE a.id='$app_id'
");

$data = mysqli_fetch_assoc($query);

if(isset($_POST['generate'])){

    $salary = floatval($_POST['salary']); // ✅ FIXED
    $joining_date = $_POST['joining_date'];

    // =============================
    // ✅ SALARY CALCULATION FIRST
    // =============================
    $ctc = $salary;

    $basic = $ctc * 0.50;
    $hra = $basic * 0.50;
    $pf_employee = $basic * 0.12;
    $pf_employer = $basic * 0.12;
    $other = $ctc - $basic - $hra - $pf_employer;
    $gross = $basic + $hra + $other;
    $net_salary = $gross - $pf_employee;


    $monthly_basic = $basic / 12;
    $monthly_hra = $hra / 12;
    $monthly_other = $other / 12;
    $monthly_gross = $gross / 12;
    $monthly_pf_employee = $pf_employee / 12;
    $monthly_pf_employer = $pf_employer / 12;
    $monthly_net = $net_salary / 12;
    $monthly_ctc = $ctc / 12;

    
    

    // Create folder
    if(!is_dir("../offers")){
        mkdir("../offers", 0777, true);
    }

    $file_name = "offer_" . time() . ".pdf";
    $file_path = "../offers/" . $file_name;

    // =============================
    // PDF START (SAME CONTENT, CLEAN FORMAT)
    // =============================
    $pdf = new FPDF();
$pdf->AddPage();

// ================= HEADER =================
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'CodeCraft Pvt. Ltd.',0,1,'R');

$pdf->SetFont('Arial','',10);
$pdf->Cell(0,5,'Nagpur, India | contact_hr@codecraft.com',0,1,'R');

// Line
$pdf->SetDrawColor(0,0,0);
$pdf->Line(10,25,200,25);

$pdf->Ln(10);

// ================= TITLE =================
$pdf->SetFont('Arial','B',18);
$pdf->Cell(0,10,'OFFER LETTER',0,1,'C');

$pdf->Ln(5);

// DATE
$pdf->SetFont('Arial','',11);
$pdf->Cell(0,8,'Date: '.date("d M Y"),0,1,'R');

$pdf->Ln(8);

// ================= TO =================
$pdf->SetFont('Arial','B',12);
$pdf->MultiCell(0,7,"To,\n{$data['name']}\n");

// SUBJECT
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Subject: Offer of Employment',0,1);

$pdf->Ln(4);

// ================= BODY =================
$pdf->SetFont('Arial','',12);
$pdf->MultiCell(0,7,
"Dear {$data['name']},\n\n".
"We are pleased to offer you the position of {$data['title']} at CodeCraft Pvt. Ltd.\n"
);

// ================= JOB DETAILS =================
$pdf->Ln(3);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Job Details:',0,1);

$pdf->SetFont('Arial','',12);
$pdf->MultiCell(0,7,
"Designation: {$data['title']}\n".
"Location: {$data['location']}\n".
"Joining Date: $joining_date\n"
);

// ================= TERMS =================
$pdf->Ln(4);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Terms & Conditions:',0,1);

$pdf->SetFont('Arial','',11);
$pdf->MultiCell(0,6,
"1. Probation period: 6 months\n".
"2. Subject to company policies\n".
"3. Confidentiality must be maintained\n"
);

// ================= CLOSING =================
$pdf->Ln(5);
$pdf->SetFont('Arial','',12);
$pdf->MultiCell(0,7,
"We look forward to welcoming you to our team and wish you a successful career with us.\n".
"You will be eligible for an annual CTC of Rs. ".number_format($ctc)." /- PA. The components of the compensation package are illustrated in Annexure I of this Offer Letter."
);

// ================= SIGNATURE =================
$pdf->Ln(5);
$pdf->Cell(0,7,'Sincerely,',0,1);

$pdf->Ln(10);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,7,'HR Manager',0,1);
$pdf->Cell(0,7,'CodeCraft Pvt. Ltd.',0,1);

// ================= ACCEPTANCE =================
$pdf->Ln(8);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Employee Acceptance',0,1);

$pdf->SetFont('Arial','',11);
$pdf->MultiCell(0,6,
"I, {$data['name']}, accept the terms and conditions of this offer.\n\n".
"Signature: ______________________ Date: __________________________\n"
);



// ================= SALARY TABLE =================
$pdf->AddPage(); // 👉 NEW PAGE (Professional touch)

// ================= HEADER =================
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'CodeCraft Pvt. Ltd.',0,1,'R');

$pdf->SetFont('Arial','',10);
$pdf->Cell(0,5,'Nagpur, India | contact_hr@codecraft.com',0,1,'R');

// Line
$pdf->SetDrawColor(0,0,0);
$pdf->Line(10,25,200,25);

$pdf->Ln(10);

// DATE
$pdf->SetFont('Arial','',11);
$pdf->Cell(0,8,'Date: '.date("d M Y"),0,1,'R');

$pdf->Ln(8);

$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,'Annexure I',0,1,'C');

$pdf->Ln(5);

// Table Header
// Header
$pdf->SetFont('Arial','B',11);
$pdf->SetX(30);
$pdf->Cell(70,10,'Component',1,0,'C');
$pdf->Cell(40,10,'Monthly',1,0,'C');
$pdf->Cell(40,10,'Annual',1,1,'C');

$pdf->SetFont('Arial','',11);

function money($val){
    return number_format($val,2);
}

// Rows
$pdf->SetFont('Arial','',11);

$pdf->SetX(30);
$pdf->Cell(70,9,'Basic Salary',1);
$pdf->Cell(40,9,money(round($monthly_basic)),1,0,'R');
$pdf->Cell(40,9,money(round($basic)),1,1,'R');

$pdf->SetX(30);
$pdf->Cell(70,9,'HRA',1);
$pdf->Cell(40,9,money(round($monthly_hra)),1,0,'R');
$pdf->Cell(40,9,money(round($hra)),1,1,'R');

$pdf->SetX(30);
$pdf->Cell(70,9,'Other Allowance',1);
$pdf->Cell(40,9,money(round($monthly_other)),1,0,'R');
$pdf->Cell(40,9,money(round($other)),1,1,'R');

$pdf->SetX(30);
$pdf->Cell(70,9,'Gross Salary',1);
$pdf->Cell(40,9,money(round($monthly_gross)),1,0,'R');
$pdf->Cell(40,9,money(round($gross)),1,1,'R');

$pdf->SetX(30);
$pdf->Cell(70,9,'Employee PF (12%)',1);
$pdf->Cell(40,9,money(round($monthly_pf_employee)),1,0,'R');
$pdf->Cell(40,9,money(round($pf_employee)),1,1,'R');

$pdf->SetX(30);
$pdf->Cell(70,9,'Employer PF (12%)',1);
$pdf->Cell(40,9,money(round($monthly_pf_employer)),1,0,'R');
$pdf->Cell(40,9,money(round($pf_employer)),1,1,'R');

// Highlight rows
$pdf->SetFont('Arial','B',11);

$pdf->SetX(30);
$pdf->Cell(70,10,'Net Take Home',1);
$pdf->Cell(40,10,money(round($monthly_net)),1,0,'R');
$pdf->Cell(40,10,money(round($net_salary)),1,1,'R');

$pdf->SetX(30);
$pdf->Cell(70,10,'Total CTC',1);
$pdf->Cell(40,10,money(round($monthly_ctc)),1,0,'R');
$pdf->Cell(40,10,money(round($ctc)),1,1,'R');

   
   // Save PDF 
   $pdf->Output('F', $file_path); 
   
   // Save in DB 
   mysqli_query($conn," UPDATE applications SET offer_letter='$file_name', status='Selected' WHERE id='$app_id' ");

    // =============================
    // EMAIL
    // =============================
    $mail = new PHPMailer(true);

    try{
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = //'your mail id';
        $mail->Password = //'App Password';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom(/*'your mail id',*/ 'CodeCraft');
        $mail->addAddress($data['email'], $data['name']);

        $mail->addAttachment($file_path);

        $mail->isHTML(true);
        $mail->Subject = "Offer Letter - {$data['title']}";
        $mail->Body = " 
        <h3>Dear {$data['name']},</h3> <p>Congratulations! 🎉</p> 
        <p>You have been selected for the position of <b>{$data['title']}</b>.
        </p> <p>Please find your offer letter attached.</p> 
        <p><strong>Best Regards,<br>CodeCraft Team</strong></p> ";

        $mail->send();

        echo "<script> alert('Offer Letter Generated & Email Sent!'); window.location='view_applications.php'; </script>";
    } 
        catch(Exception $e){ echo "<script> alert('Offer Generated but Email Failed!'); window.location='view_applications.php'; </script>";
        }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Generate Offer</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: linear-gradient(135deg, hsl(182, 80%, 90%), #69439b); min-height:100vh;">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">

    <!-- Logo + Brand -->
    <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
        <img src="../images/logo.png" alt="Logo" style="width:40px; height:40px; margin-right:10px;">
        Code Craft
    </a>
    
    <button class="btn btn-dark">
        <i class="bi bi-list"></i>
    </button>

    <div class="ms-auto dropdown">
        <a class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
           href="#"
           role="button"
           data-bs-toggle="dropdown"
           aria-expanded="false">

            <?php echo $_SESSION['name']; ?>
        </a>

        <ul class="dropdown-menu dropdown-menu-end shadow">
            <li>
                <a class="dropdown-item" href="profile.php">👤 View Profile</a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger" href="../logout.php">🚪 Logout</a>
            </li>
        </ul>
    </div>

</nav>

<div class="container mt-5">
<div class="card p-4 shadow">

<h4>Generate Offer for <?php echo $data['name']; ?></h4>

<form method="POST">

<div class="mb-3">
<label>Annual CTC (₹)</label>
<input type="number" name="salary" class="form-control" required>
</div>

<div class="mb-3">
<label>Joining Date</label>
<input type="date" name="joining_date" class="form-control" required>
</div>

<button type="submit" name="generate" class="btn btn-success">
Generate & Send Offer
</button>
<a href="view_applications.php" class="btn btn-secondary">Back</a>

</form>

</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>