<?php
  
  $page_title = "Publications – NetWIS Lab";
  $page_desc  = "Selected, full list, by years, and by areas.";
  require __DIR__ . '/partials/header.php';
  require_once __DIR__ . '/lib/pubs.php';

  $all = pubs_load_all();
  $selected  = pubs_filter($all, ['selected' => true]);
  $journals  = pubs_filter($all, ['type' => 'journal']);
  $confs     = pubs_filter($all, ['type' => 'conference']);
  $by_year   = pubs_group_by_year($all);
  $by_area   = pubs_group_by_area($all);
?>

<div class="publications">
  <h2>Publications</h2>

  <div class="tabs">
    <!-- Tab 1: Selected -->
    <div class="tab" id="selected-publications">
      <input class="tab-radio" type="radio" id="tab-1" name="tab-group-1" checked>
      <label class="tab-label" for="tab-1">Selected</label>
      <div class="tab-panel">
        <div class="tab-content">
          <?= pubs_render_paragraphs($selected, 'SP') ?>
        </div>
      </div>
    </div> <!-- end #selected-publications -->

    <!-- Tab 2: Full List -->
    <div class="tab" id="all-publications">
      <input class="tab-radio" type="radio" id="tab-2" name="tab-group-1">
      <label class="tab-label" for="tab-2">Full List</label>
      <div class="tab-panel">
        <div class="tab-content">
          <h4>Journal Papers</h4>
          <?= pubs_render_paragraphs($journals, 'JP') ?>

          <h4>Conference Papers</h4>
          <?= pubs_render_paragraphs($confs, 'CP') ?>
        </div>
      </div>
    </div> <!-- end #all-publications -->

    <!-- Tab 3: By Years -->
    <div class="tab" id="year-publications">
      <input class="tab-radio" type="radio" id="tab-3" name="tab-group-1">
      <label class="tab-label" for="tab-3">By Years</label>
      <div class="tab-panel">
        <div class="tab-content">
          <?php if (empty($by_year)): ?>
            <div class="publications"><p>No publications yet.</p></div>
          <?php else: ?>
            <?php foreach ($by_year as $year => $items): ?>
              <h4><?= (int)$year ?></h4>
              <?= pubs_render_paragraphs($items) ?>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div> <!-- end #year-publications -->

    <!-- Tab 4: By Areas -->
    <!-- <div class="tab" id="area-publications">
      <input class="tab-radio" type="radio" id="tab-4" name="tab-group-1">
      <label class="tab-label" for="tab-4">By Areas</label>
      <div class="tab-panel">
        <div class="tab-content">
          <?php if (empty($by_area)): ?>
            <div class="publications"><p>No publications yet.</p></div>
          <?php else: ?>
            <?php foreach ($by_area as $area => $items): ?>
              <h4><?= htmlspecialchars($area) ?></h4>
              <?= pubs_render_paragraphs($items) ?>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div> end #area-publications -->

  </div> <!-- end .tabs -->
</div> <!-- end .publications -->

<?php require __DIR__ . '/partials/footer.php'; ?>
