{include file="header.tpl"}
  <h2>{translate('Manage backups')}</h2>
  
  <a href="{$CAT_ADMIN_URL}/admintools/tool.php?tool=droplets">&laquo; {translate('Back to overview')} &laquo;</a><br /><br />
  
  {if $info}<div class="info ui-corner-all">{$info}</div><br />{/if}
  <form method="post" action="{$action}">
  <input type="hidden" name="tool" value="droplets" />
  <input type="hidden" name="backups" value="1" />

  {if ! count($rows)}
  <div class="info ui-corner-all">{translate('No Backups found')}</div>
  {else}
  <table class="droplets tablesorter">
	<thead>
	  <tr>
		<th>{translate('Actions')}</th>
		<th>{translate('Name')}</th>
		<th>{translate('Size')} (Byte)</th>
		<th>{translate('Date')}</th>
		<th>{translate('Files')}</th>
	  </tr>
	</thead>
	<tbody>
	{foreach $rows item}
      <tr>
        <td>
          <input type="checkbox" name="markeddroplet[]" id="markeddroplet_{$item.name}" value="{$item.name}" />
          <a href="{$CAT_ADMIN_URL}/admintools/tool.php?tool=droplets&amp;backups=1&amp;delbackup={$item.name}"><img src="{$IMGURL}/delete.png" /></a>
          <a href="{$CAT_ADMIN_URL}/admintools/tool.php?tool=droplets&amp;backups=1&amp;recover={$item.name}"><img src="{$IMGURL}/import.png" style="width:16px;" title="{translate('Import')}" /></a>
          <a href="#" class="tooltip"><img src="{$IMGURL}/info.png" alt="{translate('List contained files')}" /><span class="comment">{translate('Contained files')}:<br />{$item.listfiles}</span></a>
		</td>
        <td>
		  <a href="{$item.download}" title="{translate('Download')}">{$item.name}</a>
		</td>
        <td>{$item.size}</td>
        <td>{$item.date}</td>
        <td>{$item.files}</td>
	  </tr>
	{/foreach}
	</tbody>
  </table><br />
  {translate('marked')}:
  <input type="submit" name="delete" value="{translate('Delete')}" />
  </form>
  {/if}
{include file="footer.tpl"}