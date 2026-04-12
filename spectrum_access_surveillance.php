<?php
  $page_title = "Spectrum Access Surveillance – NetWIS Lab";
  $page_desc  = "Research on spectrum access surveillance.";
  require __DIR__ . '/partials/header.php';
?>

<div class="expandable_naration">
  <h2>Spectrum Access Surveillance</h2>
  <p>
    Our research on spectrum access surveillance focuses on how to measure, model, predict, and secure spectrum usage in modern wireless systems under realistic dynamics and deployment constraints. Across this line of work, we study high-resolution spectrum tenancy in LTE mobile access networks, develop decoding-enabled measurement and segmentation methods to uncover user and spectrum information, and investigate both binary and fine-grained prediction of spectrum occupancy for more efficient access decisions. We further examine differential spectrum mobility features, temporal and spectral distributions of spectrum holes, and surveillance from the perspectives of coverage, culprit detection, and information propagation under adversarial or uncertain conditions. Complementing these measurement and modeling efforts, we also explore practical over-the-air power domain multiple access (PDMA) systems to understand how interference, jamming, and implementation-level effects shape real-world spectrum efficiency and resilience. Overall, this direction aims to bridge theory and practice for spectrum intelligence by building a unified understanding of spectrum measurement fidelity, access prediction, surveillance robustness, and practical wireless system behavior.
  </p>

  <h4>Uncover Spectrum and User Information in LTE Mobile Access Networks</h4>
  <div class="research-content">
    <ul>
      <li>This line of work develops decoding-enabled frameworks for fine-grained LTE spectrum tenancy measurement and user-awareness in operational mobile access networks. We study how to uncover spectrum occupancy directly from real cellular signals, segment and model tenancy dynamics under time-varying traffic, characterize temporal and spectral spectrum-hole behavior, and extract differential mobility features that improve understanding of high-resolution spectrum evolution in practice.</li>
    </ul>
    <h2>Publications</h2>
    <p class="publication-item">
      Rui Zou and Wenye Wang, "FLuMe: Understanding Differential Spectrum Mobility Features in High Resolution," in IEEE Transactions on Mobile Computing, vol. 23, no. 12, pp. 14186-14200, Dec. 2024.
    </p>
    <p class="publication-item">
      Rui Zou and Wenye Wang, "Effi-Ace: Efficient and Accurate Prediction for High-Resolution Spectrum Tenancy," IEEE Conference on Computer Communications, Vancouver, BC, Canada, 2024, pp. 2199-2208.
    </p>
    <p class="publication-item">
      Rui Zou and Wenye Wang, "U-CIMAN: Uncover Spectrum and User Information in LTE Mobile Access Networks," in Proc. of IEEE INFOCOM, July 2020.
    </p>
  </div>

  <h4>Power Domain Multiple Access</h4>
  <div class="research-content">
    <ul>
      <li>Our PDMA research examines how power-domain superposition and successive interference cancellation behave beyond idealized models. By building and evaluating LTE-compliant over-the-air PDMA systems, we study the practical gap between theoretical gains and real deployment outcomes, quantify the effects of residual interference and jamming, and analyze how implementation constraints influence throughput, robustness, and attack resilience in realistic wireless environments.</li>
    </ul>
    <h2>Publications</h2>
    <p class="publication-item">
      Teng Fei and Wenye Wang, "Over-the-Air Power Domain Multiple Access in Practice," submitted to IEEE GLOBECOM, 2026.
    </p>
    <button class="collapse-button"><span data-icon=""> Collapse</span></button>
  </div>

  <h4>Spectrum Surveillance</h4>
  <div class="research-content">
    <ul>
      <li>We investigate spectrum surveillance as a system-level problem that connects measurement, monitoring, and response. Our work studies surveillance coverage, culprit detection, and the role of information dissemination in mitigating abnormal or adversarial spectrum activity, with emphasis on how communication delay, uncertainty, and conflicting information propagation affect the reliability of surveillance-driven wireless decision making.</li>
    </ul>
    <h2>Publications</h2>
    <p class="publication-item">
      Jie Wang, Wenye Wang, Cliff Wang, and Min Song, "Spectrum Activity Surveillance: Modeling and Analysis from Perspectives of Surveillance Coverage and Culprit Detection," in IEEE Transactions on Mobile Computing, October 2020.
    </p>
    <p class="publication-item">
      Jie Wang, Wenye Wang and Cliff Wang, "Modeling and Analysis of Conflicting Information Propagation in a Finite Time Horizon.," in IEEE/ACM Transactions on Networking, VOL. 28, NO. 3, pp. 972-985, June 2020.
    </p>
    <p class="publication-item">
      Jie Wang, Wenye Wang, and Cliff Wang, "SAS: Modeling and Analysis of Spectrum Activity Surveillance in Wireless Overlay Networks," in Proc. of IEEE INFOCOM, April 2019.
    </p>
    <button class="collapse-button"><span data-icon=""> Collapse</span></button>
  </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
