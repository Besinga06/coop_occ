<?php session_start(); ?>
<?php
require('db_connect.php');
$unit = "SELECT * FROM tbl_units ORDER BY unit ASC";
$result_unit = $db->query($unit);
?>
<table class="table datatable-button-html5-basic table-hover table-bordered" id="table-product">
  <thead>
    <tr style="border-bottom: 4px solid #ddd;background: #eee;color: #404040!important">
      <th>Image</th>
      <!-- <th >Product Code</th> -->
      <th>Product ID</th>
      <th>Product Name</th>
      <th> In Stock</th>
      <th>Price</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $query = "SELECT * FROM tbl_products";
    $result = $db->query($query);
    while ($row = $result->fetch_assoc()) {
      $image = $row['image'];
      if ($image != "") {
        $image_file = '../uploads/' . $image;
      } else {
        $image_file = '../images/no-image.png';
      }
    ?>
      <tr style="cursor: pointer;color: #404040!important" title="View Details" onclick="view_details(this)" product_id="<?= $row['product_id']; ?>">
        <td> <img alt="<?= $image_file ?>" style="width: 90px;height: 90px;border: 2px solid #eee" src="<?= $image_file ?>" /> </td>
        <!-- <td><img alt="<?= $product_code ?>" src="barcode.php?codetype=Code39&size=40&text=<?= $row['product_code']; ?>&print=true" /></td> -->
        <td style="width: 160px">21324<?= $row['product_id']; ?></td>
        <td><b><?= $row['product_name']; ?></b></td>
        <td style="text-align: center;"><?= $row['quantity']; ?></td>
        <td style="text-align: right;width: 160px"><?= number_format($row['selling_price'], 2); ?></td>
      </tr>
    <?php } ?>
  </tbody>
</table>

<!-- Core JS files -->
<script type="text/javascript" src="../assets/js/core/libraries/jquery.min.js"></script>
<!-- /core JS files -->
<script type="text/javascript" src="../assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript">
  $(function() {

    // Table setup
    // ------------------------------
    // Setting datatable defaults
    $.extend($.fn.dataTable.defaults, {
      autoWidth: false,
      dom: '<"datatable-header"fBl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
      language: {
        search: '<span></span> _INPUT_',
        searchPlaceholder: 'Type to search...',
        lengthMenu: '<span>Show:</span> _MENU_',
        paginate: {
          'first': 'First',
          'last': 'Last',
          'next': '&rarr;',
          'previous': '&larr;'
        }
      }
    });

    // Basic initialization
    $('.datatable-button-html5-basic').DataTable({
      "order": [
        [0, "desc"]
      ],
      buttons: {

      }
    });


  });
</script>