<?php namespace ProcessWire;

include './_func.php';

$prp_number= '2020-068';
$prp_title = 'prp_title';
$prp_authors = 'prp_authors';
$prp_publisher = '?';
echo x("tr class='hightlighMarked' origcl='b_table-zebra'",
       x("td class='align_right b_wrap'","&nbsp;").
       x("td class='b_table-zebra2 align_right prp_serial b_wrap'",$prp_number).
       x("td class='b_table-zebra2 align_left b_wrap'",
	 x("a href='https://arxiv.org/abs/2006.14756'",
	   x("span class='prp_title'",$prp_title)).'<br>'.
	 x("span class='prp_authors'",$prp_authors)));

/*
<tr class='hightlighMarked' origcl='b_table-zebra'>
  <td class='align_right b_wrap'>&nbsp;</td>
  <td class='b_table-zebra2 align_right prp_serial b_wrap'>2020-068</td>
  <td class='b_table-zebra2 align_left b_wrap'><a href='https://arxiv.org/abs/2006.14756'><span class='prp_title'>Implications of the search for optical counterparts during the second part of the Advanced LIGO's and Advanced Virgo's third observing run: lessons learned for future follow-up observations</span></a><br><span class='prp_authors'>Michael W. Coughlin, Tim Dietrich, Sarah Antier, Mouza Almualla, Shreya Anand, Mattia Bulla et al.</span></td>
  <td class='b_table-zebra2 align_left b_wrap'><a class='fancybox.iframe iframe' title='Monthly Notices of the Royal Astronomical Society, Volume 497, Issue 1, pp.1181-1196' href='https://dx.doi.org/doi:10.1093/mnras/staa1925#content'>Monthly Notices of the Royal Astronomical Society, Volume 497, Issue 1, pp.1181-1196</a></td>
 </tr>
  */ 