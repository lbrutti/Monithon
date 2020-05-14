<div class="container" id="report-list">

    <header class="row">
        <div class="col-12">
            <h1>I REPORT</h1>
            <small>Abbiamo <strong><?php echo $total_reports; ?></strong> report su Monithon!</small>
            <hr>
        </div>
    </header>

    <section class="reports">
    <?php foreach($reports as $report){ ?>
    <div class="report-list-entry">
        <div class="row">
                <div class="col-12">
                    <small><span class="report-date"><?php echo strftime('%d/%m/%Y', $report->mod_date);?></span> | <span class="report-author"><?php echo (!empty($report->autore) ? $report->autore : $report->username); ?></span></small>

                    <h2>
                        <a href="/report/view/<?php echo $report->id; ?>"><?php echo $report->titolo; ?></a>
                    </h2>
                </div>

                <div class="col-9">
                    <p class="report-description"><?php echo hellip($report->descrizione, 250); ?></p>
                </div>

                <div class="col-3">
                    <?php if(isset($report->images) && !empty($report->images)){ ?>
                    <img src="<?php echo image($report->images[0], 'cropx180'); ?>" alt="<?php echo $report->titolo; ?> - immagine">
                    <?php } ?>
                    <div class="col"><span class=" giudizio-sintetico <?php echo cssify($report->giudizio_sintetico); ?>"><?php echo $report->giudizio_sintetico; ?></span></div>
                </div>

            <div class="col-12">
                <a href="/report/view/<?php echo $report->id; ?>" class="btn btn-primary btn-sm">Leggi il report</a>
            </div>

        </div>
    </div>
    <?php } ?>
    </section>

    <section class="row" id="pagination">
        <nav aria-label="Report List Navigation" class="col-12">
            <ul class="pagination justify-content-center">


                <li class="page-item">
                    <a class="page-link <?php echo (is_null($prev_page) ? 'disabled' : ''); ?> " href="#" aria-label="Precedente">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                <?php for($i = 1; $i <= (ceil($total_reports / 10)); $i++){ ?>
                <li class="page-item <?php echo($i == $curr_page ? 'active' : ''); ?>"><a class="page-link" href="/report/index/?p=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                <?php } ?>
                <li class="page-item">
                    <a class="page-link <?php echo (is_null($next_page) ? 'disabled' : ''); ?>" href="#" aria-label="Successivo">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>

    </section>
</div>
