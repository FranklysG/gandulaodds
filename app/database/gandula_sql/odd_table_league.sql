-- CREATE OR REPLACE VIEW `view_table_odd_champions_league` AS
SELECT 
soccer_team.id
,soccer_team.slug
,soccer_team.acron
,sum((soccer_table.win*3)+(soccer_table.draw*1)) as pts
,sum((soccer_table.win)+(soccer_table.draw)+(soccer_table.los)) as j
,sum(soccer_table.win) as v
,sum(soccer_table.draw) as e
,sum(soccer_table.los) as d
,sum(soccer_table.pro_goal) as gp
,sum(soccer_table.own_goal) as gc
,sum((soccer_table.pro_goal)-(soccer_table.own_goal)) as sg
,format(round((sum(soccer_table.win*3)+sum(soccer_table.draw*1))/((sum(soccer_table.win)+sum(soccer_table.draw)+sum(soccer_table.los))*3),5)*100,1) as ap
,IFNULL(format(100*(1/format(round((sum(soccer_table.win*3)+sum(soccer_table.draw*1))/((sum(soccer_table.win)+sum(soccer_table.draw)+sum(soccer_table.los))*3),5)*100,1)),2), 2.00) as odds
FROM soccer_table
INNER JOIN soccer_team on soccer_team.id = soccer_table.soccer_team_id
INNER JOIN soccer_match on soccer_match.id = soccer_table.soccer_match_id
INNER JOIN football_league on football_league.id = soccer_match.football_league_id AND football_league.status = 1
INNER JOIN league on league.id = football_league.league_id AND league.id = 3
GROUP BY soccer_team.id
ORDER BY pts DESC