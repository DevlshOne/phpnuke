$(document).ready(function(){$(".add_vote").off('click').click(function(){var this_rel_id=$(this).attr('rel');rel_id_info=this_rel_id.split("-");var another_rel_id=(rel_id_info[1]=="m")?this_rel_id.replace("m","b"):this_rel_id.replace("b","m");var data=[];$("#"+this_rel_id+" input").each(function(index){data[index]=($(this).prop("checked"))?1:0});$.post(phpnuke_url+"index.php?modname=Surveys",{op:"pollCollector",vote_data:data,pollID:parseInt(rel_id_info[2])},function(response,status)
{response=typeof response==='object'?response:JSON.parse(response);if(response.status=='success')
{$("#"+this_rel_id).html(response.message);$('.progress .progress-bar').progressbar();if($("#"+another_rel_id).size()!=0)
$("#"+another_rel_id).html(response.message)}
else alert(response.message)})})})