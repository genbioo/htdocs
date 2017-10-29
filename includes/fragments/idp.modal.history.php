<?php
include("../../initialize.php");
includeCore();

$db_handle = new DBController();
$idp_id = $_GET['id'];

$db_handle->prepareStatement("SELECT * FROM `idp` WHERE idp.IDP_ID = :id");
$db_handle->bindVar(':id', $idp_id, PDO::PARAM_INT,0);
$idps = $db_handle->runFetch();

if(!empty($idps)) {
    foreach ($idps as $idp) {
?>
<style>
    .carousel-control {
        position:unset;
        font-size:12px;
        color:#000;
        text-align:left;
        text-shadow: unset;
    }
    .carousel-control:hover,
    .carousel-control:focus {
        color: #111;
    }
</style>
<!-- The Modal -->
<div id="myModal<?php echo($idp['IDP_ID']); ?>" class="modal fade">
    <div class="modal-idp">
        <!-- Modal content -->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close no-print" data-dismiss="modal">&times;</button>
                <h2>
                    <div class="col-md-11">
                        <?php echo($idp['Lname'].", ".$idp['Fname']." ".$idp['Mname']); ?>
                    </div>
                    <div class="col-md-1 no-print">
                        <a id= '<?php echo($idp['IDP_ID']); ?>' class ='btn btn-info btn-xs' style ='color:white' onclick='printDiv(this.id)'>
                            PRINT
                        </a>
                    </div>
                </h2>
            </div>
            <div class="modal-body">
                <p><?php echo(json_encode($idps)); ?></p>
            </div>
        </div> <!-- IDP details container -->
    </div>
</div>
<?php
                            }
}
?>