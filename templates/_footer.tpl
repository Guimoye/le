
            </div>
            <!-- END CONTENT BODY -->
        </div>
        <!-- END CONTENT -->
    </div>
    <!-- END CONTAINER -->

    <!-- BEGIN FOOTER -->
    <div class="page-footer">
        <div class="page-footer-inner"> {'Y'|date} &copy; {$stg->brand}
        </div>
        <div class="scroll-to-top">
            <i class="icon-arrow-up"></i>
        </div>
    </div>
    <!-- END FOOTER -->

    <script src="assets/global/plugins/jquery.min.js"></script>
    <script src="assets/global/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js"></script>
    <script src="assets/global/plugins/uniform/jquery.uniform.min.js"></script>
    <script src="assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>

    <script src="assets/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
    <script src="assets/global/plugins/bootbox/bootbox.min.js" type="text/javascript"></script>

    <script src="assets/global/scripts/app.min.js"></script>
    <script src="assets/layouts/layout/scripts/layout.min.js"></script>

    <script src="js/core.js?v={$v}"></script>
    <script src="js/live.js?v={$v}"></script>
    <script src="js/print.js?v={$v}"></script>

    {if isset($js)}
        {foreach item=s from=$js}
            <script src="{$s}{if strpos($s, "http") === false}?v={$v}{/if}" type="text/javascript"></script>
        {/foreach}
    {/if}

</body>
</html>