<?php $support = new Concrete\Package\Schedulizer\Src\Install\Support(Loader::db()); ?>
<style type="text/css">
    .support-group p {padding-bottom:1rem;}
</style>

<div class="support-group">
    <h3>Schedulizer</h3>
    <p>In order to provide robust support for timezones, Schedulizer makes use of some
        features sometimes not provided in shared hosting environments. It would take 3x the
        effort to build Schedulizer without using such features, and there is no plan to
        provide backwards compatibility with older systems. Depending on your hosting
        provider, you should be able to file a support ticket and request that a certain
        feature be provided, if it is missing. Tests on your system have been run below.</p>
</div>

<div class="support-group">
    <?php if($support->phpVersion()): ?>
        <h4 class="text-success"><i class="fa fa-check"></i> PHP Version 5.4&plus; <small>(Passed!)</small></h4>
    <?php else: ?>
        <h4 class="text-danger"><i class="fa fa-close"></i> PHP Version 5.4&plus;</h4>
        <p>Unfortunately your PHP version is not up to snuff. PHP Version 5.4 was released in 2013, you are running a significantly outdated version.</p>
    <?php endif; ?>
</div>

<div class="support-group">
    <?php if($support->mysqlHasTimezoneTables()): ?>
        <h4 class="text-success"><i class="fa fa-check"></i> MySQL Timezone Tables Installed <small>(Passed!)</small></h4>
    <?php else: ?>
        <h4 class="text-danger"><i class="fa fa-close"></i> MySQL Timezone Tables Unavailable</h4>
        <p>Schedulizer requires that MySQL has <a href="http://dev.mysql.com/doc/refman/5.0/en/mysql-tzinfo-to-sql.html" target="_blank">timezone tables</a> installed in order to support conversions properly. If you are running
            in a shared hosting environment (GoDaddy, BlueHost, Arvixe, etc.), your hosting provider should support this upon request.</p>
        <p>Alternatively, if you administer your own server and have <i>root</i> access to the system, you can try running the following command:</p>
        <pre>$: mysql_tzinfo_to_sql 2>/dev/null /usr/share/zoneinfo | mysql -u root --password={{root_password}} mysql 2>/dev/null</pre>
        <p>whereas <code>{{root_password}}</code> should be replaced with your root password.</p>
    <?php endif; ?>
</div>

<div class="support-group">
    <?php if($support->phpDateTimeZoneConversionsCorrect()): ?>
        <h4 class="text-success"><i class="fa fa-check"></i> PHP DateTimeZone Conversions <small>(Passed!)</small></h4>
    <?php else: ?>
        <h4 class="text-danger"><i class="fa fa-close"></i> PHP DateTimeZone Conversions</h4>
        <p>For some reason the PHP installation your site is running on is failing to convert between timezones accurately. This is most likely
            related to poor configuration defaults by your hosting provider.</p>
    <?php endif; ?>
</div>

<div class="support-group">
    <?php if($support->phpDateTimeSupportsOrdinals()): ?>
        <h4 class="text-success"><i class="fa fa-check"></i> PHP DateTime Ordinal Support <small>(Passed!)</small></h4>
    <?php else: ?>
        <h4 class="text-danger"><i class="fa fa-close"></i> PHP DateTime Ordinal Support</h4>
        <p>For some reason the PHP installation your site is running on is failing to convert between timezones accurately. This is most likely
            related to poor configuration defaults by your hosting provider.</p>
    <?php endif; ?>
</div>

<?php if( $support->phpVersion() && $support->mysqlHasTimezoneTables() && $support->phpDateTimeZoneConversionsCorrect() && $support->phpDateTimeSupportsOrdinals() ){ ?>
    <div class="alert alert-success">Woot Woot! You are good go to!</div>
<?php }else{ ?>
    <div class="alert alert-danger">Oh no... Looks like your system will not support Schedulizer. Retry after contacting your hosting provider.</div>
    <script type="text/javascript">
        $(function(){
            var $form = $('form').attr('disabled', 'disabled').find('input[type="submit"]').remove();
        });
    </script>
<?php } ?>
