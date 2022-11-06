<?php $pager->setSurroundCount(2) ?>

<nav aria-label="Page navigation">
    <ul class="pagination">
    <?php if ($pager->hasPrevious()) : ?>
        <li class="page-item first">
            <a href="<?= $pager->getFirst() ?>" aria-label="<?= lang('Pager.first') ?>" class="page-link">
                <span aria-hidden="true">
                    <i class="tf-icon bx bx-chevrons-left"></i>&nbsp;&nbsp;
                    <?= lang('Pager.first') ?>
                </span>
            </a>
        </li>
        <li class="page-item prev">
            <a href="<?= $pager->getPrevious() ?>" aria-label="<?= lang('Pager.previous') ?>" class="page-link">
                <span aria-hidden="true">
                    <i class="tf-icon bx bx-chevron-left"></i>&nbsp;&nbsp;
                    <?= lang('Pager.previous') ?> 
                </span>
            </a>
        </li>&nbsp;
    <?php endif ?>

    <?php foreach ($pager->links() as $link): ?>
        <li <?= $link['active'] ? 'class="page-item active"' : 'page-item' ?>>
            <a href="<?= $link['uri'] ?>" class="page-link">
                <?= $link['title'] ?>
            </a>
        </li>
    <?php endforeach ?>

    <?php if ($pager->hasNext()) : ?>
        <li class="page-item next">
            <a href="<?= $pager->getNext() ?>" aria-label="<?= lang('Pager.next') ?>" class="page-link">
                <span aria-hidden="true">
                    <i class="tf-icon bx bx-chevron-right"></i>&nbsp;&nbsp;
                    <?= lang('Pager.next') ?>
                </span>
            </a>
        </li>
        <li class="page-item last">
            <a href="<?= $pager->getLast() ?>" aria-label="<?= lang('Pager.last') ?>" class="page-link">
                <span aria-hidden="true"><?= lang('Pager.last') ?>
                &nbsp;&nbsp;
                    <i class="tf-icon bx bx-chevrons-right"></i>
                </span>
            </a>
        </li>
    <?php endif ?>
    </ul>
</nav>