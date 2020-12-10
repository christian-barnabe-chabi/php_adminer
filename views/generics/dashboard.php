<script src="/public/js/stats.js"></script>

<?php

use Services\Auth;

?>

<input type="hidden" id="user_token" value="<?= Auth::token() ?>">

<!-- top wedgets -->
<div class="ui grid equal width">
    <div class="column uk-text-center">
        <div class="ui raised segment" style="height: 125px !important">
            <h1 id="pme_count">...</h1>
            <span class=" uk-text-meta"><i class="ui building icon"></i>Data 1</span>
        </div>
    </div>
    <div class="column uk-text-center">
        <div class="ui raised segment" style="height: 125px !important">
            <h1 id="partner_count">...</h1>
            <span class=" uk-text-meta"><i class="building icon"></i>Data 2</span>
        </div>
    </div>
    <div class="column uk-text-center">
        <div class="ui raised segment" style="height: 125px !important">
            <h1 id="user_count">...</h1>
            <span class=" uk-text-meta"><i class="users icon"></i>Data 3</span>
        </div>
    </div>
    <div class="column uk-text-center">
        <div class="ui raised segment" style="height: 125px !important">
            <h1 class="all_tickets_count">...</h1>
            <span class=" uk-text-meta"><i class="ui calendar icon"></i>Data 4</span>
        </div>
    </div>
</div>

<!-- charts -->
<div class="ui two column grid">

    <div class="column six wide">
        <div class="ui raised segment">
            <div class="ui raised segment red uk-margin-remove-top">Chart 1</div>
            <canvas id="tickets_per_reason_count_chart" height="150"></canvas>
        </div>
    </div>
    
    <div class="column ten wide">
        <div class="ui raised segment">
            <div class="ui raised segment red uk-margin-remove-top">Chart 2</div>
            <canvas id="tickets_count_chart" height="150"></canvas>
        </div>
    </div>

</div>
