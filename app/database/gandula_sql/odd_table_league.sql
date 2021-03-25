-- CREATE OR REPLACE VIEW `view_table_odd_libertadores` AS
SELECT 
soccer_table.soccer_team_id,
soccer_team.slug,
soccer_team.acron,
((soccer_table.win*3)+(soccer_table.draw*1)) as pts,
((soccer_table.win)+(soccer_table.draw)+(soccer_table.los)) as j,
soccer_table.win as v,
soccer_table.draw as e,
soccer_table.los as d,
soccer_table.pro_goal as gp,
soccer_table.own_goal as gc,
((soccer_table.pro_goal)-(soccer_table.own_goal)) as sg,
format(round(((soccer_table.win*3)+(soccer_table.draw*1))/(((soccer_table.win)+(soccer_table.draw)+(soccer_table.los))*3),5)*100,1) as ap,
IFNULL(format(100*(1/format(round(((soccer_table.win*3)+(soccer_table.draw*1))/(((soccer_table.win)+(soccer_table.draw)+(soccer_table.los))*3),5)*100,1)),2), 2.00) as odds,
football_league.status
FROM football_league 
INNER JOIN soccer_match on soccer_match.football_league_id = football_league.id
INNER JOIN soccer_team on soccer_team.id = soccer_match.soccer_team_master_id or soccer_team.id = soccer_match.soccer_team_visiting_id
INNER JOIN soccer_table on soccer_table.soccer_team_id = soccer_team.id
WHERE football_league.status = 1
AND football_league.league_id = 2
GROUP BY soccer_table.id
ORDER BY pts DESC