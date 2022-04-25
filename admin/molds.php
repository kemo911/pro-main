<?php
/**
 * Created by PhpStorm.
 * User: Kajem
 * Date: 1/17/2018
 * Time: 5:05 PM
 */
include_once(dirname(dirname(__FILE__)) . '/classes/check.class.php');
include_once(dirname(dirname(__FILE__)) . '/admin/classes/functions.php');
$molds = getMolds();
flashMoldSession();
protect("*");
include_once 'header.php';
?>
<div class="container">
    <div class="row">
        <?php if( !empty($_SESSION['success_msg']) ): ?>
            <div class="alert alert-success fade in">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <?php echo $_SESSION['success_msg']; ?>
            </div>
            <?php
            unset($_SESSION['success_msg']);
        endif; ?>
        <div class="text-right" style="margin-bottom: 10px;">
            <a class="btn btn-info text-right"
               href="/admin/mold.php/"><i
                        class="glyphicon glyphicon-plus"></i> Créer une estimation</a>
        </div>
        <button onClick="printContent('estimateReport')">Imprimer</button><br/><br/>
        <table id="estimateReport" class="table table-striped table-bordered">
            <thead>
            <tr>
                <th scope="col"># Réclamation</th>
                <th scope="col">Nom</th>
                <th scope="col">Tél</th>
                <th scope="col">Client</th>
                <th scope="col">Date Créée</th>
                <th scope="col">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($molds)){ ?>
                <tr><td colspan="6" align="center"><strong>Aucune d'estimation.</strong></td></tr>
            <?php } else { ?>
                <?php foreach ($molds as $mold): ?>
                    <?php
                    $client_name = getClientName($mold['client_id']);
                    ?>
                    <tr>
                        <td><?php echo $mold['reclamation']; ?></td>
                        <td><?php echo $mold['f_name'] . ' ' . $mold['l_name']; ?></td>
                        <td><?php echo $mold['tel']; ?></td>
                        <td><?php echo getClientName($mold['client_id']); ?></td>
                        <td><?php echo date('Y/m/d', strtotime($mold['created_at'])); ?></td>
                        <td>
                            <a href="/admin/mold.php?mold_id=<?php echo $mold['id']; ?>" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>
                            &nbsp;&nbsp;&nbsp;
                            <a href="/admin/delete_mold.php?mold_id=<?php echo $mold['id']; ?>" title="Remove" onclick="return confirm('Vous voulez vraiment effacer?');"><span class="glyphicon glyphicon-remove"></span></a>
                        </td>
                    </tr>
                    <?php
                endforeach;
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<link href="./assets/css/mold.css?asdf2df1" rel="stylesheet">
    <script type="text/javascript">
        <!--
        function printContent(id){
            str=document.getElementById(id).innerHTML
            newwin=window.open('','printwin','left=100,top=100,width=1200,height=1200')
            newwin.document.write('<HTML>\n<HEAD>\n')
            newwin.document.write('<TITLE>Print Page</TITLE>\n')
            newwin.document.write('<script>\n')
            newwin.document.write('function chkstate(){\n')
            newwin.document.write('if(document.readyState=="complete"){\n')
            newwin.document.write('window.close()\n')
            newwin.document.write('}\n')
            newwin.document.write('else{\n')
            newwin.document.write('setTimeout("chkstate()",2000)\n')
            newwin.document.write('}\n')
            newwin.document.write('}\n')
            newwin.document.write('function print_win(){\n')
            newwin.document.write('window.print();\n')
            newwin.document.write('chkstate();\n')
            newwin.document.write('}\n')
            newwin.document.write('<\/script>\n')
            newwin.document.write('</HEAD>\n')
            newwin.document.write('<BODY onload="print_win()">\n')
            newwin.document.write(str)
            newwin.document.write('</BODY>\n')
            newwin.document.write('</HTML>\n')
            newwin.document.close()
        }
        //-->
    </script>
<?php include_once('footer.php'); ?>