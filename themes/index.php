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

  <div class="d-flex toggled" id="wrapper">

    <!-- Sidebar -->
    <div class="bg-light-black" id="sidebar-wrapper" style="width: 17rem; height: 100%;">
      <div class="sidebar-heading" style="padding: 0.5rem 1rem;">
        <b>POS</b>
      </div>
      <div class="divider-heading" style="padding: 0rem 1rem;">
        <div class="dropdown-divider" style="margin-top: 0rem;"></div>
      </div>
      <div class="list-group list-group-flush content mCustomScrollbar" style="max-width: 20rem; width: 17rem; height: 100%; padding-bottom: 0px;">
        <ul class="list-unstyled components">
          <?php if ( hasAkses('transaksi/SaldoAwalKasir') ): ?>
            <li class="">
              <a class="list-group-item list-group-item-action bg-light-black cursor-p" onclick="sak.modalSaldoAwalKasir()">
                <i class="fa fa-usd" style="width: 8%;"></i>
                <span style="width: 92%;">Saldo Awal Kasir</span>
              </a>
            </li>
          <?php endif ?>
          <?php // if ( hasAkses('transaksi/SaldoAkhirKasir') ): ?>
            <li class="">
              <a class="list-group-item list-group-item-action bg-light-black cursor-p" onclick="co.modalSaldoAkhirKasir()">
                <i class="fa fa-usd" style="width: 8%;"></i>
                <span style="width: 92%;">Saldo Akhir Kasir</span>
              </a>
            </li>
          <?php // endif ?>
          <li class="">
            <a href="#member" data-toggle="collapse" aria-expanded="false" data-val="0" class="dropdown-toggle list-group-item list-group-item-action bg-light-black cursor-p">
              <i class="fa fa-users" style="width: 8%;"></i>
              <span style="width: 92%;">Member</span>
            </a>
            <ul class="collapse list-unstyled" id="member">
              <?php if ( hasAkses('master/MemberGroup') ): ?>
                <li class="menu">
                  <a class="list-group-item list-group-item-action bg-light-black cursor-p menu" onclick="mg.modalMemberGroup()">Grup Member</a>
                </li>
              <?php endif ?>
              <?php if ( hasAkses('master/Member') ): ?>
                <li class="menu">
                  <a class="list-group-item list-group-item-action bg-light-black cursor-p menu" onclick="mbr.modalMember()">List Member</a>
                </li>
                <li class="menu">
                  <a class="list-group-item list-group-item-action bg-light-black cursor-p menu" onclick="mbr.modalSaldoMember()">Saldo Member</a>
                </li>
              <?php endif ?>
            </ul>
          </li>
          <?php if ( hasAkses('transaksi/Dapur') ): ?>
            <li class="">
              <a class="list-group-item list-group-item-action bg-light-black cursor-p" href="transaksi/Dapur">
                <i class="fa fa-usd" style="width: 8%;"></i>
                <span style="width: 92%;">Dapur</span>
              </a>
            </li>
          <?php endif ?>
          <?php if ( hasAkses('transaksi/MenuGagal') ): ?>
            <li class="">
              <a class="list-group-item list-group-item-action bg-light-black cursor-p" href="transaksi/MenuGagal">
                <i class="fa fa-trash" style="width: 8%;"></i>
                <span style="width: 92%;">Menu Gagal</span>
              </a>
            </li>
          <?php endif ?>
          <?php // if ( hasAkses('transaksi/SaldoAkhirKasir') ): ?>
            <li class="">
              <a class="list-group-item list-group-item-action bg-light-black cursor-p" href="transaksi/ClosingOrder">
                <i class="fa fa-calendar-check-o" style="width: 8%;"></i>
                <span style="width: 92%;">Closing Order</span>
              </a>
            </li>
          <?php // endif ?>

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
        <a id="menu-toggle" title="Show Menu">
          <i class="fa fa-navicon cursor-p"></i> 
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="fa fa-bars" style="font-size: 18px;"></span>
        </button>

        <div class="col-md-8 navbar-nav ml-auto pull-right" style="height: 100%;">
          <!-- <div class="col-md-1" style="padding-left: 0px;">
            <img src="assets/images/logo.png" width="90%">
          </div> -->
          <div class="col-md-11" style="padding-left: 0px; height: 100%;">
            <div class="col-md-12 p-0" style="height: 100%; display: flex; justify-content: left; align-items: center;">
              <!-- <span style="font-size: 18px;"><b><?php echo $this->config->item('nama_aplikasi'); ?></b></span> -->
              <span style="font-size: 18px;"><b><?php echo $this->session->userdata()['namaBranch']; ?></b></span>
              <!-- <br>
              <span><?php echo $this->session->userdata()['namaBranch']; ?></span> -->
            </div>
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
    var baseurl = $('head base').attr('href');
    var defaultPage = baseurl + 'transaksi/Penjualan';
    <?php if ( hasAkses('transaksi/Dapur') or hasAkses('transaksi/MenuGagal') ) { ?>
      defaultPage = baseurl + 'transaksi/Dapur';

      if ( window.location.href.indexOf("MenuGagal") > -1 ) {
        defaultPage = baseurl + 'transaksi/MenuGagal';
      } else if ( window.location.href.indexOf("Penjualan") > -1 ) {
        <?php if ( hasAkses('transaksi/Penjualan') ) { ?>
          defaultPage = baseurl + 'transaksi/Penjualan';
        <?php } ?>
      }
    <?php } ?>

    <?php if ( hasAkses('transaksi/ClosingOrder') ) { ?>
      if ( window.location.href.indexOf("ClosingOrder") >= 0 ) {
        defaultPage = baseurl + 'transaksi/ClosingOrder';
      } else if ( window.location.href.indexOf("Penjualan") > -1 ) {
        <?php if ( hasAkses('transaksi/Penjualan') ) { ?>
          defaultPage = baseurl + 'transaksi/Penjualan';
        <?php } ?>
      }
    <?php } ?>
    // var pagePembayaran = baseurl + 'transaksi/Pembayaran';

    var url = window.location.href;

    if ( window.location.href.indexOf("pembayaran") == -1 ) {
      if ( url != defaultPage ) {
        window.location.href = defaultPage;
      }
    }

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
