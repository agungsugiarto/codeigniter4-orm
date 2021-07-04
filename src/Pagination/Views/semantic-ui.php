<?php if ($paginator->hasPages()) : ?>
    <div class="ui pagination menu" role="navigation">
        <!-- Previous Page Link -->
        <?php if ($paginator->onFirstPage()) : ?>
            <a class="icon item disabled" aria-disabled="true" aria-label="<?= lang('Pager.previous') ?>"> <i class="left chevron icon"></i> </a>
        <?php else : ?>
            <a class="icon item" href="<?= $paginator->previousPageUrl() ?>" rel="prev" aria-label="<?= lang('Pager.previous') ?>"> <i class="left chevron icon"></i> </a>
        <?php endif ?>

        <!-- Pagination Elements -->
        <?php foreach ($elements as $element) : ?>
            <!-- "Three Dots" Separator -->
            <?php if (is_string($element)) : ?>
                <a class="icon item disabled" aria-disabled="true"><?= $element ?></a>
            <?php endif ?>

            <!-- Array Of Links -->
            <?php if (is_array($element)) : ?>
                <?php foreach ($element as $page => $url) : ?>
                    <?php if ($page == $paginator->currentPage()) : ?>
                        <a class="item active" href="<?= $url ?>" aria-current="page"><?= $page ?></a>
                    <?php else : ?>
                        <a class="item" href="<?= $url ?>"><?= $page ?></a>
                    <?php endif ?>
                <?php endforeach ?>
            <?php endif ?>
        <?php endforeach ?>

        <!-- Next Page Link -->
        <?php if ($paginator->hasMorePages()) : ?>
            <a class="icon item" href="<?= $paginator->nextPageUrl() ?>" rel="next" aria-label="<?= lang('Pager.next') ?>"> <i class="right chevron icon"></i> </a>
        <?php else : ?>
            <a class="icon item disabled" aria-disabled="true" aria-label="<?= lang('Pager.next') ?>"> <i class="right chevron icon"></i> </a>
        <?php endif ?>
    </div>
<?php endif ?>
