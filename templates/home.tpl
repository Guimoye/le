{include file='_header.tpl' css=[
    'assets/global/plugins/select2/css/select2.min.css',
    'assets/global/plugins/select2/css/select2-bootstrap.min.css'
]}

<h3 class="page-title"> {$page_title} </h3>

<div class="row">

    <div class="col-md-6 col-sm-6">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject bold uppercase">Recent Activities</span>
                </div>
                <div class="actions"></div>
            </div>
            <div class="portlet-body">

                {* define the function *}
                {function menu level=0}          {* short-hand *}
                    <ul class="level{$level}">
                        {foreach $data as $entry}
                            {if is_array($entry)}
                                <li>{$entry@key}</li>
                                {menu data=$entry level=$level+1}
                            {else}
                                <li>{$entry}</li>
                            {/if}
                        {/foreach}
                    </ul>
                {/function}

                {* create an array to demonstrate *}
                {$menu = ['item1',
                          'item2',
                          'item3' => ['item3-1',
                                      'item3-2',
                                      'item3-3' => ['item3-3-1',
                                                'item3-3-2']
                                      ],
                          'item4'
                         ]
                }

                {* run the array through the function *}
                {menu data=$menu}


            </div>
        </div>
    </div>

</div>

<script>
    function $Ready(){

        toastr.success('aaa');

    }
</script>

{include file='_footer.tpl' js=[
    'assets/global/plugins/select2/js/select2.full.min.js'
]}