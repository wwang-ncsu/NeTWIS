<?php
  // research.php
  $page_title = "Datasets � NetWIS Lab";
  $page_desc  = "Datasets overview of NetWIS Lab";
 
  require __DIR__ . '/partials/header.php';
?>

<div id="download-overview">
  <h2>Datasets</h2>

  <div class="download-table" style="margin-top:10px">
    
    <div class="download-table__row download-table__row--header">
      <div class="download-table__wrapper download-table__title-description">
        <div class="download-table__column download-table__title">Title</div>
        <div class="download-table__column download-table__description">Description</div>
      </div>
      <div class="download-table__column download-table__source">Source Code</div>
    </div>

    <!-- SWAMP -->
    <div class="download-table__row">
      <div class="download-table__wrapper download-table__title-description">
        <div class="download-table__column download-table__title">
          SWAMP: A Subsurface Wireless Agricultural Monitoring Platform (2024-2026)
        </div>
        <div class="download-table__column download-table__description">
          <p>
            We present SWAMP, a low-cost wireless platform for non-invasive subsurface 
            agricultural sensing using commodity radio-frequency measurements. SWAMP combines 
            repeatable wideband channel frequency response (CFR) sweeps for growth monitoring 
            with standardized Long Term Evolution (LTE) link-quality indicators for underground 
            localization and auxiliary condition sensing. A lightweight Transmission Control Protocol 
            (TCP) coordination protocol enables repeatable stepped-frequency CFR acquisition without 
            Global Positioning System-disciplined oscillators. On top of this measurement 
            pipeline, SWAMP extracts four interpretable CFR features for growth-stage inference 
            and fuses five LTE indicators through constrained optimization to recover tuber probability maps.
          </p>
          <h4>Publications</h4>
          <p class="publication-item">
            Mengning Li, Teng Fei and Wenye Wang, "SWAMP: A Subsurface Wireless Agricultural Monitoring Platform," submitted to ACM MobiCom, 2026.
          </p>
        </div>
      </div>
      <div class="download-table__column download-table__source">
        <a class="download-icon" href="https://github.com/tfei3/PSI_SRS/tree/final" target="_blank" rel="noopener">
          <span class="entypo-down">↓</span><span class="box"></span>
        </a>
      </div>
    </div>

    <!-- Hybrid Zone -->
    <div class="download-table__row">
      <div class="download-table__wrapper download-table__title-description">
        <div class="download-table__column download-table__title">
          Hybrid Zone: Bridging Acoustic and Wi-Fi for Enhanced Gesture Recognition (2023-2025)
        </div>
        <div class="download-table__column download-table__description">
          <p>
            Traditional multi-modal fusion methods often lack theoretical models due to insufficient 
            analysis of the foundational characteristics of different signals. In particular, we propose a 
            concept, Hybrid Zone, which is a theoretical model illustrating the fusion of acoustic and Wi-Fi sensing. 
            Hybrid zone offers a comprehensive perspective on the fusion of acoustic and Wi-Fi sensing areas.
          </p>
          <h4>Publications</h4>
          <p class="publication-item">
            Mengning Li and Wenye Wang, "Hybrid Zone: Bridging Acoustic and Wi-Fi for Enhanced Gesture Recognition," IEEE INFOCOM 2024 - IEEE Conference on Computer Communications, Vancouver, BC, Canada, 2024, pp. 981-990.
          </p>
        </div>
      </div>
      <div class="download-table__column download-table__source">
        <a class="download-icon" href="https://github.com/mli55/hybrid-zone" target="_blank" rel="noopener">
          <span class="entypo-down">↓</span><span class="box"></span>
        </a>
      </div>
    </div>

    <!-- U-CIMAN -->
    <div class="download-table__row">
      <div class="download-table__wrapper download-table__title-description">
        <div class="download-table__column download-table__title">
          U-CIMAN: LTE spectrum tenancy measurement (2018-2021)
        </div>
        <div class="download-table__column download-table__description">
          <p>
            When we apply U-CIMAN to LTE spectrum tenancy measurement, the experiments are conducted in two setups.
            In the first experiment setup, we use U-CIMAN to measure the spectrum usage of an LTE system fully under our control,
            where the LTE system is composed of Amarisoft LTE users and network devices. In the second setup, U-CIMAN is applied
            to measure the commercial LTE cell with the best signal strength near our lab.
          </p>
          <h4>Publications</h4>
          <p class="publication-item">
            Rui Zou and Wenye Wang, "U-CIMAN: Uncover Spectrum and User Information in LTE Mobile Access Networks," in Proc. of IEEE INFOCOM, July 2020.
          </p>
        </div>
      </div>
      <div class="download-table__column download-table__source">
        <a class="download-icon" href="https://github.com/rzrepo/U-CIMAN/" target="_blank" rel="noopener">
          <span class="entypo-down">↓</span><span class="box"></span>
        </a>
      </div>
    </div>

    <!-- Mobility Model
    <div class="download-table__row">
      <div class="download-table__wrapper download-table__title-description">
        <div class="download-table__column download-table__title">
          Mobility Model
        </div>
        <div class="download-table__column download-table__description">
          <p>
            The Semi-Markov Smooth (SMS) mobility model is a novel mobility model in which each SMS movement contains a random
            number of equal-length time steps. In each SMS movement, a node randomly selects a target direction φ<sub>α</sub> and a
            target speed v<sub>α</sub> as the expected direction and speed of the movement, and experiences a random pause time after each SMS movement.
          </p>
          <a href="<?= $BASE_URL ?>mobility-model.php">Read more »</a>
        </div>
      </div>
      <div class="download-table__column download-table__source">
       
      </div>
    </div> -->

    <!-- STEP2 in WLANs
    <div class="download-table__row">
      <div class="download-table__wrapper download-table__title-description">
        <div class="download-table__column download-table__title">
          STEP2 WLANs
        </div>
        <div class="download-table__column download-table__description">
          <p>
            Our objective is to realize the implementation of Self-TunEd Performance and Protection (STEP2) management system for
            wireless LANs. Although the architecture of STEP2 is generalized for various kinds of wireless networks, as a case study
            we evaluate STEP2 in WLAN settings under good/poor links, multiple clients, and mobility scenarios.
          </p>
          <a href="<?= $BASE_URL ?>step2-wlans.php">Read more »</a>
        </div>
      </div>
      <div class="download-table__column download-table__source">
        
      </div>
    </div> -->

    <!-- Sensor Networks
    <div class="download-table__row">
      <div class="download-table__wrapper download-table__title-description">
        <div class="download-table__column download-table__title">
          Sensor Networks
        </div>
        <div class="download-table__column download-table__description">
          <p>
            A Personal INdoor Sensor (PINS) network is deployed in homes/buildings to monitor indoor conditions and human daily behavior.
            Nodes (e.g., PIR, temperature, acoustic) communicate via multi-hop wireless links. Placement/orientation should be carefully
            designed to cover the field of interest.
          </p>
          <a href="<?= $BASE_URL ?>sensor-networks.php">Read more »</a>
        </div>
      </div>
      <div class="download-table__column download-table__source">
        
      </div>
    </div> -->

    <!-- Multihop Networks -->
    <!-- <div class="download-table__row">
      <div class="download-table__wrapper download-table__title-description">
        <div class="download-table__column download-table__title">
          Multihop Networks
        </div>
        <div class="download-table__column download-table__description">
          <p>
            Ad-hoc networks are self-organizing systems without pre-established infrastructure, envisioned for real-time applications
            (e.g., vehicular networks). Since intermediate nodes forward messages as routers, routing protocols are crucial; hence
            understanding their performance is necessary.
          </p>
          <a href="<?= $BASE_URL ?>multihop-networks.php">Read more »</a>
        </div>
      </div>
      <div class="download-table__column download-table__source">
       
      </div>
    </div> -->

    <!-- FREEDM (DESD with Zigbee)
    <div class="download-table__row">
      <div class="download-table__wrapper download-table__title-description">
        <div class="download-table__column download-table__title">
          FREEDM: Control and Monitoring of DESD Using Zigbee
        </div>
        <div class="download-table__column download-table__description">
          <p>
            The Distributed Energy Storage Device (DESD) is an essential Smart Grid component. Each battery pack (with MCU) reports status
            (e.g., temperature) and controls charge/discharge; an ARM-based system aggregates multiple packs and reports to the control center.
            Wireless (Zigbee) fits dynamic pack count/size/location, offering low power, peer-to-peer communication, and industrial adoption.
          </p>
        </div>
      </div>
      <div class="download-table__column download-table__source">
       
      </div>
    </div> -->

  </div><!-- /.download-table -->
</div><!-- /#download-overview -->

<?php require __DIR__ . '/partials/footer.php'; ?>



