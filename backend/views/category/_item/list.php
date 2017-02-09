<div class="col-sm-3">
    <div class="easyui-panel" style="padding:5px">
        <ul class="easyui-tree">
            <li>
                <span>Category</span>
                <?php
                    foreach ($listMenu as $r) {
                        echo  $r;
                    }
                ?>
            </li>
        </ul>
    </div>
</div>