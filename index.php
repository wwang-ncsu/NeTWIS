<?php
  $page_title = "NetWIS Lab";
  $page_desc  = "The Networking of Wireless Information Systems, NetWIS, laboratory led by Dr. Wenye Wang...";
  $active     = "home";
  require_once __DIR__ . '/lib/pubs.php';
  require __DIR__ . '/partials/header.php';

  $latest_pubs = pubs_latest(pubs_load_all(), 5);
?>

<div id="home">
  <div>
    <p>
      Wenye Wang received her Ph.D. degree from Electrical and Computer Engineering, Georgia Institute of Technology,
      with Dr. Ian F. Akyildiz in Atlanta, Georiga. Her Ph.D. thesis is on location management in heterogeneous
      wireless networks. In Fall 2002, she joined the Department of Electrical and Computer Engineering, NC State
      University, as an assistant professor and has been an associate professor since Fall 2008, and a full professor
      since Fall 2014. Her current research interests include modeling and performance evaluation of integrated sensing
      and communications/networking, cyber-physical systems or Internet of Things (IoT) prototype, and AI-native design
      of mobile and wireless networks. Dr. Wang received NSF CAREER Award in 2006. She is an ACM member and an IEEE
      Fellow.
    </p>
  </div>

  <div class="about-netwis">
    <h2 class="with-icon" data-icon="">About NETWIS</h2>
    <p>
      The Networking of Wireless Information Systems, NetWIS, laboratory led by Dr. Wenye Wang, is focused broadly on
      in-depth understanding, algorithm and protocol design in mobile wireless networks. Our vision is that by developing
      new models, measuring experimental results, and understanding basic properties of wireless networks in different
      circumstances, it is possible to design algorithms, protocols, and architectures that enable a wireless network to
      have robust architecture and topology and high performance for diversified applications and large-scale distributed,
      intelligent systems.
    </p>
  </div>

  <div class="latest-publications">
    <h2 class="with-icon" data-icon="">Latest Publications</h2>
    <ul>
      <?php foreach ($latest_pubs as $pub): ?>
        <?php
          $title = htmlspecialchars(pubs_clean_text((string)$pub['title']));
          $authors = htmlspecialchars(pubs_clean_text((string)$pub['authors']));
          $venue = htmlspecialchars(pubs_clean_text((string)$pub['venue']));
          $href = !empty($pub['link']) ? htmlspecialchars($pub['link']) : '#';
          $extra = trim((string)($pub['extra'] ?? ''));
        ?>
      <li>
        <a href="<?= $href ?>" target="_blank">
          <h3 class="title"><?= $title ?></h3>
        </a>
        <p><?= $authors ?>, <?= $venue ?><?php if ($extra !== ''): ?> <span class="publication-extra"><?= $extra ?></span><?php endif; ?></p>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div> <!-- end #home -->

<?php require __DIR__ . '/partials/footer.php'; ?>
