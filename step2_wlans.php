<?php
  // people.php
  $page_title = "Datasets – NetWIS Lab";
  $page_desc  = "Datasets - Step 2";
  $active     = "Datasets";
  require __DIR__ . '/partials/header.php';
?>

<div class="expandable_naration">

	<h2>STEP2 in WLANs</h2>	

	<h4 id="1._Objectives">Objectives</h4>
	<div>
	 <p>Our main objective is to realize the implmentation of Self-TunEd Performance and  Protection (STEP2) management system for wireless LANs. Although, the  architecture of STEP2 is generalized in the way that it can be used for any  kind of wireless networks, however, as a case study, we are evaluating STEP2 in  wireless LAN settings. The reason is that wireless LANs are the most popular  kind of wireless network deployed as hot-spots in the real world. Further, we  aim to carry-out experiments in different scenarios, such as under good and  poor links, with multiple clients and mobile clients settings. We plan to  measure different performance parameters such as packet loss and delay in  different scenarios to evaluate the usefulness of STEP2. We hope to setup the  testbed and different scenarios as close to real-time as possible.</p>
	 <button class="collapse-button"><span data-icon="&#xe621;"> Collapse</span></button>
  </div>

	<h4 id="2._Architecture">System Description</h4>
	<div>
  	<p>The testbed consists of a security policy server behind an access point, and many mobile clients at different locations. The server is setup as a desktop machine, which provides negotiation of security policies for clients in the wireless LANs. The server does not require any changes for implementing STEP2. The clients are laptop computers, and run STEP2 implementation. STEP2 system consists of three modules: a monitor, a decision-maker and a tuner. A brief description of these modules is as follows.</p>
  	
  	<h5>STEP2 Modules</h5>
  	<p><strong><em>Monitor:</em></strong> The monitor module collects statistics such as packet losses and delay over wireless links to measure the channel performance. The monitor module uses unicast probing technique to determine wireless channel performance between the client and the server. The monitor, then, provides the feedback to the decision-maker at regular intervals.</p>
  	<p><strong><em>Decision-Maker:</em></strong> Whenever the decision-maker module obtains feedback from the monitoring system, it runs an algorithm to determine the decisions regarding the tuning of security policies. The monitor and decision-maker are executed as background processes so that they do not interfere with the ongoing data transmission in a system. The decision-maker sends its decision to the tuner.
  	</p>
  	<p><strong><em>Tuner:</em></strong> This module takes care of changing the current security policy if the decision sent by the decision-maker module includes new security policy. Since this module runs in foreground, it adds extra overhead to the ongoing data transmission. However, our implementation ensures to keep the overhead as little as possible.</p>
	 <button class="collapse-button"><span data-icon="&#xe621;"> Collapse</span></button>
  </div>

	<h4 id="3._Setup"></a>Setup and Configuration</h4>
	<div>
	 <p>The policy server and access point are on the 3rd floor of EB2 building of the campus and are connected over wired ethernet. On the other side, clients are scattered on the 2nd and 3rd floors of the same building. The access point and clients use channel 6, and data rate has been set as 11 Mbps for all clients. The 2nd floor has a campus wireless network, however there is no wireless network on the 3rd floor except some students using their personal mobile devices. This setting helps us in obtaining measurements while having variations in the link connectivity between client and access points. As variations in link connectivity are more during afternoon due to the high flow of students, measurements are taken during afternoon as well as in nights when there are not many variations.</p>

		<h5>1. Security Policies</h5>
		<p>Currently, the testbed is configured with four IPSec security policies which are IPSec-AES-SHA1, IPSec-AES-MD5, IPSec-3DES-SHA1 and IPSec-3DES-MD5. We use Openswan implementation of IPSec in the testbed, so all details related to IPSec are specific to Openswan. In general, IPSec can be configured in tunnel and transport modes, however we have used tunnel mode configuration as it is considered stronger than the transport mode. IPSec establishes tunnels between two end points, the server and the clients in our testbed, in two phases. In the first phase, called Main Mode, cryptographic keys to be used in the second phase are generated. In the second phase, called Quick Mode, encryption and hashing algorithms are negotiated between the two end points and cryptographic keys are generated to be used for data transmission.</p>

		<h5>2. Hardware Details</h5>
		<p>The server is Dell PC with Pentium IV (2.6 GHZ). We have used Cisco Access Points (Cisco Aironet 1200 series) to provide wireless connectivity. The mobile clients are Dell Laptop with Celeron Processor (2.4GHZ). In addition, we have used Lucent orinoco gold wireless cards (802.11b) in mobile clients.</p>

		<h5>3. Software Details</h5>
		<p>All systems run Redhat Linux 9 with kernel version 2.4.20-8. Openswan open source is installed on the server and mobile clients for IPSec functionality. OpenSSL open source software is installed on all systems to be used by IPSec protocol suite. Iperf, Ping and Rude are used for generating different traffics, such as TCP and UDP.</p>
	 <button class="collapse-button"><span data-icon="&#xe621;"> Collapse</span></button>
  </div>
	
	<h4 id="4._useful_lniks">Useful Links</h4>
  <div>
  	<ul>
  		<li>
  			<a href="http://www.openswan.org/">Openswan IPSec</a><span style="FONT-WEIGHT: bold"></span>
  		</li>
  		<li>
  			<span style="FONT-WEIGHT: bold"></span><a href="http://www.openssl.org/">OpenSSL</a>
  		</li>
  		<li>
  			<a href="http://www.hping.org/">hping (Another ping utility)</a>
  		</li>
  		<li>
  			<a href="http://dast.nlanr.net/Projects/Iperf/">iPerf (TCP traffic generator)</a>
  		</li>
  		<li>
  			<a href="http://rude.sourceforge.net/">Rude (UDP traffic generator)</a>
  		</li>
  		<li>
  			<a href="http://www.nongnu.org/orinoco/">Orinoco device drivers</a>
  		</li>
  	</ul>
    <button class="collapse-button"><span data-icon="&#xe621;"> Collapse</span></button>
  </div>


	<p>If you have any question, please feel free to contact <a style="FONT-FAMILY: arial" href="http://www4.ncsu.edu/%7Eakagarwa">
				Avesh Agarwal</a>.<p>

</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
