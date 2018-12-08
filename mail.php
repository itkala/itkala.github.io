<?php
require("sendgrid-php.php");
require "apikey.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["request-form"])) {
        $company = $_POST["company"];
        $plan = $_POST["plan"];
        $phone = $_POST["phone"];
        $message = $_POST["message"];
        $email = $_POST["email"];
        $mailMessage = "<h4>Hello, $company</h4><br>";
        $mailMessage .= getPlanContent($plan);
        $subject = "$plan Request";

        $teddMessage = "<h5>$company requested for the $plan Package. Below are their details</h5>";
        $teddMessage .= "Company Name: $company <br> Email Address: $email <br>Phone Number: $phone<br> Plan: $plan<br>";
        if ($message != "" or $message != " ")
            $teddMessage .= "Message: $message";

        $a = sendMail($email, $company, "info@teddinsight.com", "info@teddinsight", $subject, $mailMessage);
        $b = sendMail("info@teddinsight.com", "info@teddinsight", $email, $company, $subject, $teddMessage);
        if ($a and $b)
            echo 1;
        else
            echo 2;
    } else if (isset($_POST["contact-form"])) {
        $name = $_POST["name"];
        $phone = $_POST["phone"];
        $company = $_POST["company"];
        $plan = "Custom";
        $message = $_POST["message"];
        $email = $_POST["email"];
        $teddSubject = "$name from $company sent you a message";
        $clientSubject = "We got your message";
        $clientMailMessage = "Thanks for reaching out to us. We will get back to you as soon as possible";
        $teddMailMessage = "<h5>$name from $company 's message details</h5>";
        $teddMailMessage .= "Name: $name <br> Company Name: $company <br> Email Address: $email <br> Phone Number: $phone <br> Message: $message<br>";
        if (isset($_POST["services"])) {
            $services = $_POST["services"];
            $clientSubject = "Custom Plan Request";
            $teddSubject = $clientSubject;
            $teddMailMessage = "<h5>$company requested for the $plan Package. Below are their details</h5>";
            $teddMailMessage .= "Company Name: $company <br> Email Address: $email<br> Plan: $plan<br>
            <h5>Below are the features requested</h5>".getCustomPlanContent($services);
            $subject = "Custom Plan Request Form";
            $clientMailMessage = "<p>We got your request for our Custom Plan package. We will get back to you as soon as possible. Thanks.</p>";
            $clientMailMessage .= "<h5>Below are the features you requested</h5>" . getCustomPlanContent($services);
        }
        $a = sendMail($email, $company, "info@teddinsight.com", "info@teddinsight", $clientSubject, $clientMailMessage);
        $b = sendMail("info@teddinsight.com", "info@teddinsight", $email, $name, $teddSubject, $teddMailMessage);
        if ($a and $b)
            echo 1;
        else
            echo 2;
    }
}

function sendMail($to, $toName, $from, $fromName, $subject, $message)
{
    $email = new \SendGrid\Mail\Mail();
    $email->setFrom($from, $fromName);
    $email->setSubject($subject);
    $email->addTo($to, $toName);
    $email->addContent("text/plain", "Hello $toName");
    $email->addContent(
        "text/html", $message
    );
    $sendgrid = new \SendGrid(apiKey());
    try {
        $response = $sendgrid->send($email);
        $statusCode = intval($response->statusCode());
        if ($statusCode >= 200 and $statusCode <= 299)
            return true;
        return false;
    } catch (Exception $e) {
        echo 'Caught exception: ' . $e->getMessage() . "\n";
        return false;
    }
}

function getPlanContent($plan)
{
    switch ($plan) {
        case "Premium Plan":
            $arr = array("FREE DIRECTORY LISTING",
                "Community Management",
                "Content Development",
                "Insight and Analytics Report",
                "Web Maintenance",
                "Brand Awareness",
                "Competitor Analysis",
                "Search Engine Optimization");
            $price = "100000";
            break;

        case "Classic Plan":
            $arr = array("FREE DIRECTORY LISTING",
                "Community Management",
                "Content Development",
                "Insight and Analytics Report",
                "Web Maintenance",
                "Brand Awareness");
            $price = "65000";
            break;
        case "Basic Plan":
            $arr = array("FREE DIRECTORY LISTING",
                "Community Management",
                "Content Development",
                "Insight and Analytics Report",);
            $price = "35000";
            break;
        default:
            return "";
    }
    $m = "<p>We got your request for our $plan package. We will get back to you as soon as possible. Thanks.</p>";
    $m .= "<h5>Below are the features of the $plan Package</h5>";
    $m .= "<ul>";

    foreach ($arr as $item) {
        $m .= "<li>$item</li>";
    }
    $m .= "</ul><br>";
    $m .= "<h6>Cost: $price</h6>";
    return $m;
}

function getContactUsMessage()
{

}

function getCustomPlanContent($services)
{

    $m = "<ul>";
    foreach ($services as $service) {
        $m .= "<li>$service</li>";
    }
    $m .= "</ul><br>";
    return $m;
}