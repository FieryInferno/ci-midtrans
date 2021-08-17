<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pembayaran</title>
  <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-1gUtnXSu6YUOwNFg"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
</head>
<body>
  <form id="payment-form" method="post" action="<?=site_url()?>/pembayaran/finish">
    <input type="hidden" name="result_type" id="result-type" value=""></div>
    <input type="hidden" name="result_data" id="result-data" value=""></div>
    <label for="id_penawaran_jasa">Id Penawaran Jasa</label>
    <input type="text" name="id_penawaran_jasa" id="id_penawaran_jasa">
    <label for="harga">Harga</label>
    <input type="text" name="harga" id="harga">
  </form>
  <button id="pay-button">Pay!</button>
  <table border="1">
    <thead>
      <tr>
        <th>No</th>
        <th>Id Penawaran Jasa</th>
        <th>Gross Amount</th>
        <th>Transaction Time</th>
        <th>Transaction Status</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $no = 1;
        foreach ($pembayaran as $key) { ?>
          <tr>
            <td><?= $no++; ?></td>
            <td><?= $key['id_penawaran_jasa']; ?></td>
            <td><?= $key['gross_amount']; ?></td>
            <td><?= $key['transaction_time']; ?></td>
            <td><?= $key['transaction_status']; ?></td>
          </tr>
        <?php }
      ?>
    </tbody>
  </table>
  <script type="text/javascript">
    $('#pay-button').click(function (event) {
      event.preventDefault();
      $(this).attr("disabled", "disabled");
      var id_penawaran_jasa = document.getElementById('id_penawaran_jasa').value;
      var harga             = document.getElementById('harga').value;
      $.ajax({
        url   : '<?=site_url()?>/pembayaran/token',
        cache : false,
        type  : 'POST',
        data  : {
          id_penawaran_jasa : id_penawaran_jasa,
          harga             : harga
        },
        success: function(data) {
          //location = data;

          console.log('token = '+data);
          
          var resultType = document.getElementById('result-type');
          var resultData = document.getElementById('result-data');

          function changeResult(type,data){
            $("#result-type").val(type);
            $("#result-data").val(JSON.stringify(data));
            //resultType.innerHTML = type;
            //resultData.innerHTML = JSON.stringify(data);
          }

          snap.pay(data, {
            
            onSuccess: function(result){
              changeResult('success', result);
              console.log(result.status_message);
              console.log(result);
              $("#payment-form").submit();
            },
            onPending: function(result){
              changeResult('pending', result);
              console.log(result.status_message);
              $("#payment-form").submit();
            },
            onError: function(result){
              changeResult('error', result);
              console.log(result.status_message);
              $("#payment-form").submit();
            }
          });
        }
      });
    });
  </script>
</body>
</html>
