
<?php
  $page_title = "Mobility Induced and Performance Modeling - NetWIS Lab";
  $page_desc  = "Research on mobility induced and performance modeling.";
  require __DIR__ . '/partials/header.php';
?>


    <div class="expandable_naration">
      <h2>Mobility Induced and Performance Modeling</h2>
      <p>
        Current projects in this direction focus on mobility induced and performance modeling.
      </p>

      <h4>Node Behavior and Network Topology</h4>
      <div class="research-content">
        <ul>
          <li>Compared with wired network, wireless ad hoc networks are more vulnerable to malicious attacks as well as failures due to their unique features, such as constrained energy, error-prone communication media and highly dynamic network topology. Especially, every node (or end user) may have quite different “social” behaviors. For example, selfish nodes refuse to forward packets for other nodes in order to save their own energy. While, with no intention for energy-saving, malicious nodes may launch all kinds of denial-of-service (DoS) attacks by replaying, reordering or/and dropping packets from time to time, and even by sending fake routing messages.</li>
        <li>As we known, the cooperation of mobile nodes is critical to the normal operation of multi-hop wireless networks, thus all aforementioned misbehaviors have prompted open challenges to many issues, such as protocol design, service availability, and topology management, in mobile ad hoc networks. Therefore, we are investigating node behaviors in order to design a general node model so that we can have an in-depth understanding on the evolution of different node behaviors and their impact to network survivability. The modeling and analysis of node behaviors will yield new insights into the analysis and design of resilient wireless multi-hop networks.</li>
        </ul>
        <h2>Publications</h2>
    <p class="publication-item">
      Fei Xing and Wenye Wang, " On the survivability of Wireless Ad Hoc Networks in the Presence of Routing Malfunction," in preparation for journal submission. April 2007.
    </p>
     <p class="publication-item">
     Fei Xing and Wenye Wang, " Modeling and Analysis of Connectivity in Mobile Ad Hoc Networks with Misbehaving Nodes," in Proc. of IEEE International Conference on Communications (ICC '07), Vol. 4, pp. 1879-1884, June 2006.
    </p>
        <button class="collapse-button"><span data-icon=""> Collapse</span></button>
      </div>
      <h4>Link and Path Dynamics in Multihop Networks</h4>
      <div class="research-content">
        <ul>
          <li>Understanding the link and path stochastic properties can greatly help researchers design mobility-resilient MANETs, maximize routing performance, optimize topology control, and achieve the desired network performance.</li>
      <li>We are focused on developing new models to study link and path properties under the circumstances such as varying radio link conditions, node mobility, geographical constraints, and interferences. In particular, we aim to study probabilistic and  statistical properties of link/path lifetime, link/path reliability in the presence of node or link failures due to radio channels and node mobility. Currently, we are developing an analytical model of the reliability problem in wireless ad hoc networks, as well as algorithms for computing two-terminal reliability in mobile ad hoc networks.</li>  
        </ul>
        <h2>Publications</h2>
    <p class="publication-item">
      Ming Zhao and Wenye Wang, "Analyzing Topology Dynamics in Ad Hoc Networks Using A Smooth Mobility Model," in the Proc. of IEEE Wireless Communications and Networking Conference (WCNC), March 2007.
    </p>
     <p class="publication-item">
      Shawqi Kharbash and Wenye Wang, "Computing Two-Terminal Reliability in Mobile Ad hoc Networks," in the Proc. of IEEE Wireless Communications and Networking Conference (WCNC), March 2007.
    </p>
        <button class="collapse-button"><span data-icon=""> Collapse</span></button>
      </div>
      <h4>Mobility Modeling and Characterization</h4>
      <div class="research-content">
        <ul>
          <li>Mobility modeling is a fundamental issue in wireless mobile networks, which has a significant impact on research areas, such as routing protocol design, network performance evaluation, link and path lifetime analysis, network connectivity study and network topology control. Therefore, we are actively working on how to design a mobility model which can effectively mimic smooth transient moving behaviors of mobile nodes and have the desired steady state properties, such as stable node speed and uniform node distribution, is a challenging issue. We aim to design a mobility model which can i) achieve the above goals, ii) integrate a variety of nice properties of existing mobility models and iii) be flexible to mimic the realistic network scenarios.</li>
        <li>In order to provide better performance and quality of service, we develop a framework to capture user mobility profile (UMP) which is a combination of historic records and predictive patterns of mobile terminals and to estimate service patterns and locations of mobile users, including descriptions of location, mobility, and service requirements. We aimed to develop new mobility model to characterize not only stochastic behaviors, but historical records and predictive future locations of mobile users as well, that is, to incorporate aggregate history and current system parameters to acquire UMP. We are also interested in the characterization of mobility uncertainty and its impact on network topology.</li>
        </ul>
        <h2>Publications</h2>
    <p class="publication-item">
      Ming Zhao and Wenye Wang, "A Unified Mobility Model for Analysis and Simulation of Mobile Wireless Networks," submitted for journal publications, December 2006.
    </p>
     <p class="publication-item">
      Ming Zhao and Wenye Wang, "A Novel Semi-Markov Smooth Mobility Model for Mobile Ad Hoc Networks," in the Proc. of IEEE GLOBECOM'06, (Best Paper Award), San Francisco, CA, November 2006.
    </p>
     <p class="publication-item">
      Ian F. Akyildiz and Wenye Wang, "A Predictive User Mobility Profile Framework for Wireless Multimedia Networks," in IEEE/ACM Transactions on Networking, vol. 12, no. 6, pp. 1021-1035. Dec. 2004.
    </p>
        <button class="collapse-button"><span data-icon=""> Collapse</span></button>
      </div>
      <h4>Vulneribility, reseilience and robustness</h4>
      <div class="research-content">
        <ul>
          <li></li>
        </ul>
        <button class="collapse-button"><span data-icon=""> Collapse</span></button>
      </div>
    </div><!-- /.expandable_naration -->

<?php require __DIR__ . '/partials/footer.php'; ?>
