<?php 

$start_time = microtime(true);
include './includes/connect.php';

$ip_addr = $_SERVER['REMOTE_ADDR'];

if (isset($_POST['submit']))
{
    $ip_val = strip_tags($_POST['ip_val']);
    $data_country = get_ip_country($ip_val);

    if (isLocalIP($ip_val) || !isValidIPv4($ip_val))
        $msg_error = '<div class="invalid-feedback">Silakan masukan alamat IPv4 publik.</div>';

    elseif ($data_country == '')
        $msg_error = '<div class="invalid-feedback">Tidak dapat menemukan IP Address dari Anda.</div>';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DB IPv4 Country | Andsp.ID</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        a {
            text-decoration: none;
        }
        .content {
            margin: 6em auto;
            max-width: 700px;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="content d-flex align-items-center">
        <div class="container">
            <div class="mb-5">
                <div class="text-center mb-5">
                    <h1>DB IPv4 Country</h1>
                    <p class="text-muted">Cari nama negara dari IP Address yang kamu punya.</p>
                </div>
    
                <form method="post" class="text-center">
                    <?php

                    $valid_input_ip = isset($ip_val) && isValidIPv4($ip_val);

                    ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Masukan IP Address</label>
                        <input type="text" class="form-control <?=isset($msg_error) ? ' is-invalid' : ''?>" placeholder="<?=$valid_input_ip ? $ip_val : $ip_addr?>" value="<?=$valid_input_ip ? $ip_val : ''?>" name="ip_val" />
                        <?=($msg_error ?? '')?>
                    </div>
                    <button class="btn btn-primary" name="submit">Submit</button>
                </form>

                <div class="text-center mt-4">
                    Total Range alamat IP Yang dapat di cari<br/>

                    <?php

                    $total_ip = "SELECT SUM(ip_count) as total_ip FROM `ip_country`";
                    $total_ip = $pdo->prepare($total_ip);
                    $total_ip->execute();
                    $fetch_total = $total_ip->fetch(PDO::FETCH_OBJ);

                    echo '<b>'.number_format(($fetch_total->total_ip ?? 0), 0, ',', '.').'</b>';

                    ?>
                </div>

                <?php if (isset($data_country)): ?>
                    <div class="mt-5">
                        <h4>Output</h4>

                        <div class="my-4">
                        <?php
                        
                        $data_country = array_merge(['ip' => $ip_val], (array) $data_country);
                        $output = json_encode($data_country, JSON_PRETTY_PRINT);

                        ?>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <span class="fw-bold">IP Address</span>
                            </div>
                            <div class="col-md-9">
                                <?=htmlspecialchars($data_country['ip'])?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <span class="fw-bold">Kode Negara</span>
                            </div>
                            <div class="col-md-9">
                                <?=htmlspecialchars($data_country['kode'] ?? '-')?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <span class="fw-bold">Negara</span>
                            </div>
                            <div class="col-md-9">
                                <?=htmlspecialchars($data_country['negara'] ?? '-')?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <span class="fw-bold">JSON Output</span>
                            </div>
                            <div class="col-md-9">
                                <?=isset($output) ? '<pre>'.$output.'</pre>' : '-' ?>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            </div>

            <hr/>

            <div class="my-5">
                <h4>Negara dari IP Kamu</h4>

                <?php if (isIPLocalhost($ip_addr)): ?>
                    <div class="alert alert-danger my-4">
                        Anda sedang menggunakan IP Looback atau localhost. Tidak dapat mencari data dari IP Anda.
                    </div>
                <?php else: ?>

                    <div class="my-4">
                        <?php
                        
                        $get_country = get_ip_country($ip_addr);

                        if ($get_country)
                        {
                            $get_country = array_merge(['ip' => $ip_addr], (array) $get_country);
                            $json_output = json_encode($get_country, JSON_PRETTY_PRINT);
                        }

                        ?>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <span class="fw-bold">IP Address</span>
                            </div>
                            <div class="col-md-9">
                                <?=$ip_addr?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <span class="fw-bold">Kode Negara</span>
                            </div>
                            <div class="col-md-9">
                                <?=htmlspecialchars($get_country['kode'] ?? '-')?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <span class="fw-bold">Negara</span>
                            </div>
                            <div class="col-md-9">
                                <?=htmlspecialchars($get_country['negara'] ?? '-')?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <span class="fw-bold">JSON Output</span>
                            </div>
                            <div class="col-md-9">
                                <?=isset($json_output) ? '<pre>'.$json_output.'</pre>' : '-' ?>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            </div><br/>

            <div class="text-center">
                <p>
                    Dibuat oleh <a href="https://www.andsp.id/" target="_blank">Andsp.ID</a><br/>

                    <?php

                    $end_time = microtime(true);
                    $execution_time = $end_time - $start_time;

                    ?>

                    Page generated in <?=number_format($execution_time, 3)?> seconds.
                </p>
                <a href="https://github.com/andspdev/dbipv4-country" target="_blank">Github.com</a>
            </div>

        </div>
    </div>
</body>
</html>

<?php $pdo = null ?>