<!DOCTYPE html>
<html lang="en">

<head>
  <base href="<?php echo base_url() ?>" />
  <!-- <link rel="shortcut icon" type="image/x-icon" href="assets/images/logo.png"> -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="Dashboard">
  <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">

  <title>
    <?php echo $this->config->item('judul_aplikasi'); ?>
  </title>

  <?php // CSS files ?>
  <?php if (isset($css_files) && is_array($css_files)) : ?>
      <?php foreach ($css_files as $css) : ?>
          <?php if ( ! is_null($css)) : ?>
              <link rel="stylesheet" href="<?php echo $css; ?>?v=<?php echo $this->settings->site_version; ?>"><?php echo "\n"; ?>
          <?php endif; ?>
      <?php endforeach; ?>
  <?php endif; ?>

</head>

<body>

  <div class="d-flex" id="wrapper">

    <!-- Sidebar -->
    <div class="bg-light-black" id="sidebar-wrapper" style="width: 17rem;">
      <!-- <div class="sidebar-heading">
        <img src="assets/images/logo-mgb.jpg" width="20%" height="20%">
        MGB POS
      </div> -->
      <div class="divider-heading" style="padding: 0rem 1rem;">
        <div class="dropdown-divider" style="margin-top: 0rem;"></div>
      </div>
      <div class="list-group list-group-flush content mCustomScrollbar" style="max-width: 20rem; width: 17rem;">
        <ul class="list-unstyled components">
          <li class="active">
            <a class="list-group-item list-group-item-action bg-light-black menu" data-txt="Dashboard" href="#">
              <i class="fa fa-dashboard"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <!-- <?php $arr_fitur = $this->session->userdata()['Fitur']; ?>
          <?php foreach ($arr_fitur as $key => $v_fitur): ?>
            <li>
              <a href="<?php echo '#'.$v_fitur['id_header_fitur'] ?>" data-toggle="collapse" aria-expanded="false" data-val="0" class="dropdown-toggle list-group-item list-group-item-action bg-light-black">
                <?php echo $v_fitur['header_fitur']; ?>
              </a>
              <ul class="collapse list-unstyled" id="<?php echo $v_fitur['id_header_fitur'] ?>">
                <?php foreach ($v_fitur['detail'] as $key => $v_mdetail): ?>
                  <li class="menu">
                    <a href="<?php echo $v_mdetail['path_detfitur']; ?>" class="list-group-item list-group-item-action bg-light-black menu" data-txt="<?php echo $v_mdetail['nama_detfitur']; ?>"><?php echo $v_mdetail['nama_detfitur']; ?></a>
                  </li>
                <?php endforeach ?>
              </ul>
            </li>
          <?php endforeach ?> -->
        </ul>
      </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper">

      <nav class="navbar navbar-expand-lg navbar-light bg-light no-padding">
        <a id="menu-toggle" title="Hide Menu">
          <i class="fa fa-navicon cursor-p"></i> 
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="col-md-8 navbar-nav ml-auto pull-right" style="height: 100%;">
          <!-- <div class="col-md-1" style="padding-left: 0px;">
            <img src="assets/images/logo.png" width="90%">
          </div> -->
          <div class="col-md-11" style="padding-left: 0px;">
            <div class="col-md-12 p-0" style="font-size: 18px;"><b><?php echo $this->config->item('nama_aplikasi'); ?></b></div>
            <div class="col-md-12 p-0"><?php echo $this->session->userdata()['namaBranch']; ?></div>
          </div>
        </div>

        <div class="col-md-4 collapse navbar-collapse" id="navbarSupportedContent" style="height: 100%!important;">
          <ul class="navbar-nav ml-auto pull-right" style="width: 100%; height: 100%;">
            <li id="header_notification_bar" class="nav-item dropdown" style="width: 100%; height: 100%;">
              <div class="col-md-10 p-0" style="height: 100%;">
                <div class="col-md-12 p-0 text-right" style="font-size: 18px; height: 100%; display: flex; justify-content: right; align-items: center;">
                  <span><b><?php echo $this->session->userdata()['detail_user']['nama_detuser']; ?></b></span>
                </div>
              </div>
              <?php
                $src = 'uploads/icon-user.png';
                if ( isset($this->session->userdata()['detail_user']['avatar_detuser']) ) {
                  $src = 'uploads/'.$this->session->userdata()['detail_user']['avatar_detuser'];
                }
              ?>
              <div class="col-md-1 no-padding pull-right" style="height: 100%;">
                <div data-toggle="dropdown" class="col-md-12 p-0" style="height: 100%; display: flex; justify-content: right; align-items: center;">
                  <img src="uploads/icon-user.png" class="img-circle cursor-p pull-right" aria-expanded="true" width="30" height="30">
                </div>
                <ul class="dropdown-menu dropdown-menu-right extended notification">
                  <li class="dropdown-item setting">
                    Setting
                  </li>
                  <div class="dropdown-divider no-padding"></div>
                  <li class="dropdown-item">
                    <a class="cursor-p" onclick="modalHelp()">
                      <i class="fa fa-cog m-r-5 m-l-5"></i>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Help
                    </a>
                  </li>
                  <li class="dropdown-item">
                    <a class="cursor-p" data-toggle="modal" data-target="#logoutModal">
                      <i class="fa fa-power-off m-r-5 m-l-5"></i>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Logout
                    </a>
                  </li>
                </ul>
              </div>
            </li>
          </ul>
        </div>
      </nav>

      <div class="container-fluid">

        <div class="main">
          <?php echo $view; ?>
        </div>
      </div>
    </div>
    <!-- /#page-content-wrapper -->

  </div>
  <!-- /#wrapper -->

  <!-- Logout Modal-->
  <div class="modal" id="logoutModal">
    <div class="modal-dialog">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Alert</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
          <span class="modal-title">Apakah anda yakin ingin keluar ?</span>
        </div>

        <!-- Modal footer -->
        <div class="modal-footer">
          <a class="btn btn-primary logout" href="user/Login/logout">Ya</a>
          <button data-dismiss="modal" class="btn btn-danger" type="button">Tidak</button>
        </div>

      </div>
    </div>
  </div>

  <?php // Javascript files ?>
  <?php if (isset($js_files) && is_array($js_files)) : ?>
      <?php foreach ($js_files as $js) : ?>
          <?php if ( ! is_null($js)) : ?>
              <?php echo "\n"; ?><script type="text/javascript" src="<?php echo $js; ?>?v=<?php echo $this->settings->site_version; ?>"></script><?php echo "\n"; ?>
          <?php endif; ?>
      <?php endforeach; ?>
  <?php endif; ?>

  <!-- Menu Toggle Script -->
  <script>
    $("#menu-toggle").click(function(e) {
      e.preventDefault();
      $("#wrapper").toggleClass("toggled");
      var togled = $("#wrapper").attr('class').split(" ");

      if ( togled.length > 1 ) {
        $("#wrapper").find('a#menu-toggle').attr('title', 'Show Menu');
        $("#wrapper").find('i.left').attr('hidden', true);
        $("#wrapper").find('i.right').removeAttr('hidden');
        $(".tu-float-btn-left").removeClass('toggled');
      } else {
        $("#wrapper").find('a#menu-toggle').attr('title', 'Hide Menu');
        $("#wrapper").find('i.left').removeAttr('hidden');
        $("#wrapper").find('i.right').attr('hidden', true);
        $(".tu-float-btn-left").addClass('toggled');
      };
    });

    $(".dropdown-toggle").click(function(e) {
      $(this).closest('li').toggleClass("open");
    });

    (function($){
      $(window).on("load",function(){
        
        $("#content-1").mCustomScrollbar({
          theme:"minimal"
        });
        
      });
    })(jQuery);

    function modalHelp (elm) {
        $('.modal').modal('hide');

        $.get('transaksi/Penjualan/modalHelp',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '50%', 'max-width': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                $(this).find('.btn-tesprint').click(function() {
                    $.ajax({
                        url: 'transaksi/Penjualan/printTes',
                        data: {},
                        type: 'POST',
                        dataType: 'JSON',
                        beforeSend: function() {},
                        success: function(data) {
                            if ( data.status != 1 ) {
                                bootbox.alert(data.message);
                            }
                        }
                    });
                });
            });
        },'html');
    }
  </script>

</body>

</html>
