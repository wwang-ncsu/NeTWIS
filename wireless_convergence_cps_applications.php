<?php
  $page_title = "Wireless Convergence and CPS Applications – NetWIS Lab";
  $page_desc  = "Research on wireless convergence and CPS applications.";
  require __DIR__ . '/partials/header.php';
?>

<div id="fundamental-properties" class="expandable_naration">
  <h2>Wireless Convergence and CPS Applications</h2>
  <p>
    The convergence of wireless communication, multimodal sensing, and cyber-physical intelligence is reshaping how next-generation systems perceive, interact with, and optimize the physical world. Our group is committed to advancing the fundamental theory, system design, and real-world deployment of wireless convergence technologies that bridge communication, sensing, and control across diverse CPS applications. With an emphasis on cross-modal wireless sensing, intelligent inference, and resilient system integration, our research explores how ubiquitous RF, Wi-Fi, acoustic, and cellular signals can be synergized to enable robust human-centered environments, smart infrastructure, precision agriculture, and critical cyber-physical services. Areas of current research interest include the following:
  </p>

  <h4>Multimodal Wireless Sensing and Intelligent Environment Modeling</h4>
  <div class="research-content">
    <ul>
      <li>Multimodal wireless sensing is emerging as a fundamental paradigm for next-generation intelligent environments, enabling continuous, non-intrusive perception of human behaviors, spatial dynamics, and contextual events. By leveraging ubiquitous Wi-Fi and acoustic signals, our research focuses on how to establish unified theoretical foundations and scalable system architectures for robust long-term sensing in real-world environments. In particular, we are actively working on how to fuse heterogeneous sensing modalities to overcome the limitations of individual technologies, such as drift accumulation in tracking, environment dependency in gesture recognition, and fragmentation across isolated sensing tasks. Our goal is to develop sensing frameworks that can i) provide interpretable theoretical models for multimodal signal interaction, ii) support stable long-term human tracking and fine-grained activity inference, and iii) seamlessly scale to integrated multi-task intelligent environment applications such as smart homes, ambient healthcare, and context-aware automation.</li>
    </ul>
    <button class="collapse-button"><span data-icon=""> Collapse</span></button>
    <h2>Publications</h2>

    <p class="publication-item">
      Mengning Li and Wenye Wang, "DuTrack: Long-Term Indoor Human Tracking with Dual-Channel Sensing and Inference," accpepted by Infocom, 2026.
    </p>

    <p class="publication-item">
      Mengning Li and Wenye Wang, "UNI-FI: Integrated Multi-Task Wi-Fi Sensing," accpepted by Infocom, 2026.
    </p>
    <p class="publication-item">
      Mengning Li and Wenye Wang, "Synergizing Acoustic and Wi-Fi Signals for Device-Free Gesture Recognition," in IEEE Transactions on Mobile Computing, 2025.
    </p>
  </div>

  <h4>Subsurface Wireless Agricultural Monitoring Platform</h4>
  <div class="research-content">
    <ul>
      <li>Subsurface wireless agricultural sensing is a promising direction for precision agriculture, enabling non-invasive monitoring of crop growth and underground conditions without destructive sampling. Our SWAMP work focuses on developing a practical wireless platform for subsurface agricultural monitoring using commodity radio-frequency measurements. In particular, we study how wideband channel frequency response (CFR) sweeps and LTE link-quality indicators can be jointly leveraged to support growth-stage inference, underground localization, and auxiliary soil-condition sensing. Our goal is to build a low-cost and deployable system that can i) provide repeatable sensing measurements without specialized high-end instrumentation, ii) support interpretable feature extraction and constrained inference for below-ground crop monitoring, and iii) enable practical agricultural sensing workflows in greenhouse and controlled soil-diverse environments.</li>
    </ul>
    <h2>Publications</h2>
    <p class="publication-item">
      Mengning Li, Teng Fei and Wenye Wang, "SWAMP: A Subsurface Wireless Agricultural Monitoring Platform," submitted to ACM MobiCom, 2026.
    </p>
    <p class="publication-item">
      Technical Report, "Non-Invasive Detection and Management of Underground Tuber Plants with RF Signals and Stereo Imaging," sponsored by iCons.
    </p>
    <button class="collapse-button"><span data-icon=""> Collapse</span></button>
  </div>

  <h4>Cross-Layer Dashboard Responsiveness in Connected Electric Vehicles</h4>
  <div class="research-content">
    <ul>
      <li>Modern electric vehicle dashboards integrate navigation maps, route updates, battery status, media content, and driver-assistance widgets under strict rendering deadlines. Our work studies dashboard freezing as a cross-layer timing problem caused by the temporal interaction among wireless communication delay, packet jitter, CPU scheduling, DVFS behavior, and frame-level rendering deadlines. We develop a unified framework for synchronized telemetry collection, freeze-risk modeling, online freeze prediction, and adaptive rendering control, enabling proactive mitigation actions such as resolution scaling, frame-rate adaptation, and selective deferral of non-essential updates. This direction aims to improve dashboard responsiveness, reduce user-visible stutter and freezing, and support more reliable software-defined vehicle interfaces under weak wireless links, high system load, and dynamic workload conditions.</li>
    </ul>
    <button class="collapse-button"><span data-icon=""> Collapse</span></button>
  </div>

  <h4>Smart Grid</h4>
  <div class="research-content">
    <ul>
      <li>Our smart grid research examines how communication reliability, timing, and security shape the resilience of interdependent power and cyber infrastructures. Across this line of work, we study cascading-failure propagation, communication-assisted control, and adversarial disruption in smart grid environments, with emphasis on how message dissemination, delay, and network impairment affect system-wide stability. We develop modeling and co-simulation frameworks to capture the interaction between power flow evolution and communication behavior, quantify when communication helps or harms failure mitigation, and design mechanisms that improve timely delivery of critical control information under stressed or hostile conditions.</li>
    </ul>
    <h2>Publications</h2>
    <p class="publication-item">
      Mingkui Wei, Zhuo Lu, and Wenye Wang, "On Characterizing Information Dissemination During City-Wide Cascading Failures in Smart Grid," in IEEE Systems Journal, pp. 1-10, September 2017.
    </p>
    <p class="publication-item">
      Zhuo Lu, Wenye Wang, and Cliff Wang, "Camouflage Traffic: Minimizing Message Delay for Smart Grid Applications under Jamming," in IEEE Transactions on Dependable and Secure Communications (TDSC), Vol. 12, No. 1, pp. 31-44, January 2015.
    </p>
    <p class="publication-item">
      Mingkui Wei and Wenye Wang, "Dominoes with Communications: On Characterizing the Progress of Cascading Failures in Smart Grid," in Proc. of IEEE ICC, May 2016.
    </p>
    <p class="publication-item">
      Xiang Lu, Wenye Wang, Jianfeng Ma, and Limin Sun, "Domino of the Smart Grid: An Empirical Study of System Behaviors in the Interdependent Network Architecture," in Proc. of IEEE SmartGridComm, pp. 1-6, October 2013.
    </p>
    <button class="collapse-button"><span data-icon=""> Collapse</span></button>
  </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
