<?php
  // people.php
  $page_title = "Datasets – NetWIS Lab";
  $page_desc  = "Datasets - Multihop Networks";
  $active     = "Datasets";
  require __DIR__ . '/partials/header.php';
?>

<div class="expandable_naration">
  <h2>Multihop Networks</h2>
  <p> Ad-hoc networks, being multihop in nature, are characterized as a self-organizing system of wireless devices requiring no pre-established infrastructure. Therefore, they are being envisioned for many real-time applications, such as communications for moving vehicles as in vehicular networks, for rescue at disastrous sites and in military battlefield, and so on. Since in ad-hoc networks, intermediate nodes act as routers in forwarding messages between nodes not in communication range of each other, routing protocols play an invaluable role for data transmissions. Therefore, it becomes necessary to have better understanding of the performance of existing routing protocols for ad-hoc networks. For the same, we establish a real-time ad-hoc testbed to understand the in-depth properties of routing protocols in ad-hoc networks. </p>

  <h4 id="1._Objectives">Objectives</h4>
  <div>
    <p>It is studied that network dynamics such as link hysteresis and node mobility in wireless ad-hoc environments lead to frequent communication failures. Due to that, routing overhead is a major hindrance to configuring real-time application, such as VoIP, in ad-hoc environment, because control packets may impose severe quality of service (QoS) degradation. Therefore, our objective is to understand the impact of varied packet sizes and hop-counts in a real-time ad-hoc network by setting-up an ad-hoc testbed. Specifically, our objective is to study how routing overhead, packet size and hop-count interact with each other, and to what extent they affect end-to-end delay and throughput. Therefore, we aim to obtain real-time measurements in different scenarios, and by using those results we aim to develop a statistical-analytic approach to studying the effects of routing protocols on delay and throughput. </p>
    <button class="collapse-button"><span data-icon="&#xe621;"> Collapse</span></button>
  </div>

  <h4 id="2._Architecture">Architecture</h4>
  <div>
    <p> The real-time testbed consists of various mobile devices, such as iPAQ, Sharp Zaurus, laptop and desktop machines each equipped with wireless cards, consisting different hardware and software platforms. The different types of hardware devices helps us obtaining real-time measurements which will be applicable to heterogeneous environment as well. All wireless nodes communicate in ad-hoc mode, and data streams with varied packet sizes and the testbed topologies consisting of varied number of hops are used to generate several load conditions in the experimental setup. Although, the testbed can be used to evaluate any ad-hoc routing protocol, as a case study, we study optimized link state routing (OLSR) protocol and ad-hoc on demand vector routing protocol (AODV). </p> 
    <button class="collapse-button"><span data-icon="&#xe621;"> Collapse</span></button>
  </div>
  
  <h4 id="3._Setup">Setup and Configuration</h4>
  <div>
    <p>In ad-hoc networks, link failures are generally due to node mobility and link hysteresis. To emulate link failure behavior in the indoor environment, intermediate nodes along the paths are switched on and off once every 10 seconds during data transmission among nodes. In our experiments, every scenario consists of an alternate path to continue data transmission in case of link failure events. The IPtables module is configured to achieve physical separation among nodes that are more than one-hop away by using their medium access control (MAC) addresses. In addition, the transmission rate for each wireless card has been set to 11 Mbps. Access point ID on all nodes is set to a unique value to enable them communicate in ad-hoc mode. The wirelesschannel shared by all nodes are set to 10, whereas the campus network uses wireless channel 1, to avoid interference from the campus wireless network. </p>

    <h6>Hardware</h6>
    <ul>
      <li> Desktop: Dell PC, Pentium IV 2.6 GHZ (RHL 9, kernel version 2.4.20). <span style="font-weight: bold;"></span> </li>
      <li> iPAQ: Intel StrongARM 206 MHx (Familiar Linux 0.6, kernel version 2.4.18-rmk3). <span style="font-weight: bold;"></span> </li>
      <li> Sharp Zaurus: Intel XScale 400 MHz (Linux Embedix kernel version 2.4.18-rmk7-pxa3-embedix). <span style="font-weight: bold;"></span> </li>
      <li> Dell Laptop MN: Celeron Processor 2.4GHZ (RHL 9, kernel version 2.4.20).<span style="font-weight: bold;"></span> </li>
      <li> Wireless Cards: Netgear MA 311 802.11b, Lucent Orinoco Gold 802.11b.<span style="font-weight: bold;"></span>&nbsp;<em><small style="font-weight: bold;"><span style="font-weight: normal;"></span></small></em></li>
    </ul>

    <h6>Software </h6> 

    <ul>
      <li> Kernel AODV version 2.1 from NIST. <span style="font-weight: bold;"></span> </li>
      <li> OLSR version 0.4.5. <span style="font-weight: bold;"></span> </li>
      <li> IPtables for filtering packet at MAC level. <span style="font-weight: bold;"></span></li>
      <li> Ethereal network packet analyzer. <span style="font-weight: bold;"></span> </li>
      <li> Netperf, ttcp, Rude data transmission utilities. <span style="font-weight: bold;"></span></li>
    </ul>
    <button class="collapse-button"><span data-icon="&#xe621;"> Collapse</span></button>
  </div>

  <h4 id="4._useful_lniks">Related Links</h4>
  <div>
    <ul>
      <li> <a href="http://www.olsr.org/">OLSR </a> <span style="font-weight: bold;"></span> </li>
      <li>  <span style="font-weight: bold;"></span><a href="http://w3.antd.nist.gov/wctg/aodv_kernel/">AODV</a> </li>
      <li> <a href="http://familiar.handhelds.org/">Familiar Linux </a> <span style="font-weight: bold;"></span> </li>
      <li> <a href="http://www.netfilter.org/">IPtables </a> <span style="font-weight: bold;"></span> </li>
      <li> <a href="http://www.netperf.org/netperf/">Netperf </a> <span style="font-weight: bold;"></span> </li>
      <li> <a href="http://www.ethereal.com/">Ethereal </a> <span style="font-weight: bold;"></span> </li>
      <li> <a href="http://www.hping.org/">hping (Another ping utility)</a> </li>
      <li> <a href="http://dast.nlanr.net/Projects/Iperf/"> iPerf (TCP traffic generator)</a> </li>
      <li> <a href="http://rude.sourceforge.net/">Rude (UDP traffic generator)</a></li>
      <li> <a href="http://www.nongnu.org/orinoco/">Orinoco device drivers</a></li>
      <li> <a href="http://en.wikipedia.org/wiki/IPAQ/"> iPAQ device drivers</a></li>
      <li> <a href="http://en.wikipedia.org/wiki/Sharp_Zaurus"> Sharp zaurus device drivers</a> </li>
    </ul>
    <button class="collapse-button"><span data-icon="&#xe621;"> Collapse</span></button>
  </div>

  <h4 id="5._pubs">Publications</h4>
  <div>
    <ol>
      <li> Avesh Agarwal and Wenye Wang, "<span style="font-weight: bold;">Statistical Analysis of the Impact of Routing in MANET Based on Real-Time Measurements,</span>" <span style="font-style: italic;"> in Proc. of the 14th&nbsp;International Conference on Computer Communication and Networks (ICCCN'05),</span> San Diego , California , USA , October, 2005. (<strong>Nominated for Best Paper Award</strong>)</li>
    </ol>
    <button class="collapse-button"><span data-icon="&#xe621;"> Collapse</span></button>
  </div>

  <p>If you have any question, please feel free to contact <a href="http://www.ncsu.edu/%7Eakagarwa"> Avesh Agarwal</a>.

</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
