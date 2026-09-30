<?php
require 'config.php';
$pdo = db();
$pdo->exec("CREATE TABLE IF NOT EXISTS users (
 id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, username TEXT UNIQUE NOT NULL,
 password_hash TEXT NOT NULL, role TEXT NOT NULL CHECK(role IN ('admin','trainee')), active INTEGER DEFAULT 1
)");
$pdo->exec("CREATE TABLE IF NOT EXISTS modules (
 id INTEGER PRIMARY KEY, title TEXT NOT NULL, description TEXT NOT NULL, pass_mark INTEGER NOT NULL DEFAULT 60
)");
$pdo->exec("CREATE TABLE IF NOT EXISTS questions (
 id INTEGER PRIMARY KEY AUTOINCREMENT, module_id INTEGER NOT NULL, type TEXT NOT NULL DEFAULT 'mcq',
 question TEXT NOT NULL, options_json TEXT, answer TEXT NOT NULL, marks INTEGER NOT NULL DEFAULT 1,
 FOREIGN KEY(module_id) REFERENCES modules(id)
)");
$pdo->exec("CREATE TABLE IF NOT EXISTS attempts (
 id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL, module_id INTEGER NOT NULL,
 started_at TEXT NOT NULL, submitted_at TEXT, score REAL DEFAULT 0, total REAL DEFAULT 0, status TEXT DEFAULT 'in_progress',
 FOREIGN KEY(user_id) REFERENCES users(id), FOREIGN KEY(module_id) REFERENCES modules(id)
)");
$pdo->exec("CREATE TABLE IF NOT EXISTS responses (
 id INTEGER PRIMARY KEY AUTOINCREMENT, attempt_id INTEGER NOT NULL, question_id INTEGER NOT NULL,
 answer TEXT, correct INTEGER DEFAULT 0, marks_awarded REAL DEFAULT 0,
 UNIQUE(attempt_id,question_id), FOREIGN KEY(attempt_id) REFERENCES attempts(id) ON DELETE CASCADE,
 FOREIGN KEY(question_id) REFERENCES questions(id)
)");

$mods = [
1=>['Month 1 – Computer Fundamentals & Windows','Computer basics, hardware/software, Windows, files and folders, typing, shortcuts, maintenance and safety'],
2=>['Month 2 – Microsoft Word','Document creation, typing, formatting, pictures, shapes, page layout, tables and printing'],
3=>['Month 3 – Microsoft Excel','Data entry, formatting, formulas, functions, charts, sorting/filtering, budgets, invoices and records'],
4=>['Month 4 – PowerPoint, Publisher, Internet & Email','Presentations, publication design, browsing, email, attachments and safe online practice'],
5=>['Month 5 – Networking, Cybersecurity & Online Services','Networking, Wi-Fi troubleshooting, cybersecurity, e-Citizen, KRA, online applications, CVs and Canva']
];
foreach($mods as $id=>$m){
    $s=$pdo->prepare("INSERT OR IGNORE INTO modules(id,title,description,pass_mark) VALUES(?,?,?,60)");
    $s->execute([$id,$m[0],$m[1]]);
}
$users = [
['Trainer','admin','admin123','admin'],
['Alfred','alfred','alfred123','trainee'],
['Benson','benson','benson123','trainee'],
['Pudens','pudens','pudens123','trainee'],
['Elizabeth','elizabeth','elizabeth123','trainee']
];
foreach($users as $u){
    $s=$pdo->prepare("INSERT OR IGNORE INTO users(name,username,password_hash,role) VALUES(?,?,?,?)");
    $s->execute([$u[0],$u[1],password_hash($u[2],PASSWORD_DEFAULT),$u[3]]);
}

$count=(int)$pdo->query("SELECT COUNT(*) FROM questions")->fetchColumn();
if($count===0){
$qs = [
1=>[
['mcq','Which of the following is computer hardware?',['Keyboard','Microsoft Word','Windows','Excel'],'Keyboard',1],
['mcq','What is the main purpose of an operating system?',['To manage computer hardware and software','To print documents only','To create passwords only','To browse websites only'],'To manage computer hardware and software',1],
['mcq','Which shortcut copies selected text or a file?',['Ctrl + X','Ctrl + C','Ctrl + V','Ctrl + Z'],'Ctrl + C',1],
['mcq','What is the best reason for organizing files into folders?',['To make files easier to locate and manage','To make the computer heavier','To increase monitor brightness','To remove the keyboard'],'To make files easier to locate and manage',1],
['mcq','Which device is primarily used to enter text?',['Monitor','Keyboard','Speaker','Projector'],'Keyboard',1],
['mcq','A computer is very slow. Which is a sensible first step?',['Check running programs and available storage','Replace the monitor immediately','Delete Windows','Disconnect the keyboard'],'Check running programs and available storage',1],
['mcq','Which practice helps protect computer equipment?',['Keep liquids away from the equipment','Block ventilation openings','Pull cables roughly','Switch off power by unplugging randomly'],'Keep liquids away from the equipment',1],
['mcq','Which Windows action changes a file name without changing its contents?',['Rename','Format','Install','Print'],'Rename',1],
['mcq','Why is saving work regularly important?',['It reduces the risk of losing recent work','It increases internet speed','It changes the keyboard layout','It removes viruses automatically'],'It reduces the risk of losing recent work',1],
['mcq','A trainee accidentally deletes a file. What should they check first?',['Recycle Bin','Taskbar clock','Screen brightness','Printer tray'],'Recycle Bin',1],
],
2=>[
['mcq','Which command makes selected text darker?',['Bold','Italic','Underline','Align'],'Bold',1],
['mcq','Which alignment places text evenly between the left and right margins?',['Left','Right','Center','Justify'],'Justify',1],
['mcq','Which Word feature is appropriate for arranging information in rows and columns?',['Table','Header only','Zoom','Status bar'],'Table',1],
['mcq','What does Print Preview allow you to do?',['See how a document is likely to look when printed','Delete the document','Change Windows password','Connect to Wi-Fi'],'See how a document is likely to look when printed',1],
['mcq','Which page orientation is wider than it is tall?',['Portrait','Landscape','Normal','Square'],'Landscape',1],
['mcq','A notice heading needs to stand out. Which combination is appropriate?',['Larger font and bold','Smaller font and hidden text','Delete the heading','Use only spaces'],'Larger font and bold',1],
['mcq','Which feature can place an image inside a Word document?',['Insert Picture','Page Break only','Word Count','Status Bar'],'Insert Picture',1],
['mcq','Why are margins useful?',['They control the blank space around the page content','They delete paragraphs','They create email accounts','They calculate totals'],'They control the blank space around the page content',1],
['mcq','A two-page document has an unwanted blank page. What should you inspect first?',['Extra paragraph marks or page/section breaks','The computer speakers','Wi-Fi password','Printer ink level'],'Extra paragraph marks or page/section breaks',1],
['mcq','Which action creates a separate editable copy while keeping the original?',['Save As with a new filename','Shut down','Print only','Delete'],'Save As with a new filename',1],
],
3=>[
['mcq','In Excel, the intersection of a row and column is called a…',['Cell','Slide','Paragraph','Folder'],'Cell',1],
['mcq','Which formula adds values in cells B2 to B10?',['=SUM(B2:B10)','=ADD(B2:B10)','=TOTAL(B2:B10)','=PLUS(B2:B10)'],'=SUM(B2:B10)',1],
['mcq','Which function calculates the mean of a group of numbers?',['AVERAGE','MAX','MIN','COUNT'],'AVERAGE',1],
['mcq','Which function returns the largest value?',['MIN','MAX','COUNT','AVERAGE'],'MAX',1],
['mcq','Which function returns the smallest value?',['MIN','MAX','SUM','COUNT'],'MIN',1],
['mcq','Which function counts numeric entries?',['COUNT','SUM','MAX','MIN'],'COUNT',1],
['mcq','Which Excel feature can show only records matching a condition?',['Filter','Merge only','Spell Check','Slide Show'],'Filter',1],
['mcq','Which chart is commonly useful for comparing values across categories?',['Column chart','Password chart','Folder chart','Email chart'],'Column chart',1],
['mcq','A cyber shop wants to calculate total daily sales from many entries. What should it use?',['SUM','MIN','MAX','COUNT'],'SUM',1],
['mcq','Why would sorting a customer table be useful?',['It can organize records by a selected field','It deletes formulas','It installs Excel','It changes the monitor resolution'],'It can organize records by a selected field',1],
],
4=>[
['mcq','In PowerPoint, a theme mainly controls…',['The overall visual design of slides','The computer password','The email address','The Wi-Fi signal'],'The overall visual design of slides',1],
['mcq','What is a slide transition?',['An effect when moving from one slide to another','A spreadsheet formula','An email attachment','A printer setting'],'An effect when moving from one slide to another',1],
['mcq','What is an animation applied to?',['An object or text on a slide','A Wi-Fi router only','A spreadsheet cell only','A computer cable'],'An object or text on a slide',1],
['mcq','Which application is suitable for creating a flyer?',['Publisher','Calculator','Task Manager','File Explorer'],'Publisher',1],
['mcq','Which email feature sends a file together with a message?',['Attachment','Wallpaper','Recycle Bin','Slide Show'],'Attachment',1],
['mcq','What does BCC generally do?',['Hides recipients in the BCC field from other recipients','Deletes the message','Blocks the internet','Prints the email'],'Hides recipients in the BCC field from other recipients',1],
['mcq','What is a search engine used for?',['Finding information on the web','Formatting a hard disk','Creating a keyboard','Printing envelopes'],'Finding information on the web',1],
['mcq','A presentation has too much text on each slide. What is a sensible improvement?',['Reduce text and use concise points/visuals','Make every word tiny','Remove all headings','Add more paragraphs'],'Reduce text and use concise points/visuals',1],
['mcq','You receive an unexpected attachment from an unknown sender. What should you do?',['Avoid opening it until its safety is verified','Open it immediately','Forward it to everyone','Disable antivirus'],'Avoid opening it until its safety is verified',1],
['mcq','Which is an appropriate reason to use Print Preview?',['To check layout before printing','To create a Wi-Fi network','To calculate Excel formulas','To reset a password'],'To check layout before printing',1],
],
5=>[
['mcq','A computer network is mainly used to…',['Allow connected devices to communicate and share resources','Increase screen size','Replace the keyboard','Create paper documents automatically'],'Allow connected devices to communicate and share resources',1],
['mcq','If one computer cannot connect to Wi-Fi but others can, what is a reasonable first check?',['Check that Wi-Fi is enabled and the correct network is selected','Replace every router','Delete all files','Turn off all other computers'],'Check that Wi-Fi is enabled and the correct network is selected',1],
['mcq','Which is a strong password practice?',['Use a long, unique password for each important account','Use 123456 everywhere','Share passwords publicly','Use your name only'],'Use a long, unique password for each important account',1],
['mcq','What is malware?',['Malicious software designed to harm, disrupt or misuse systems','A type of printer','A Word document','A network cable'],'Malicious software designed to harm, disrupt or misuse systems',1],
['mcq','Why should software and security tools be updated?',['Updates can fix vulnerabilities and improve security','Updates always delete files','Updates remove the need for passwords','Updates stop all internet use'],'Updates can fix vulnerabilities and improve security',1],
['mcq','Which service is associated with government online services in Kenya?',['eCitizen','PowerPoint','Paint','Notepad'],'eCitizen',1],
['mcq','What should a job applicant prepare before applying for many online jobs?',['A clear, current CV and relevant supporting information','A Wi-Fi router','A new monitor','A PowerPoint animation'],'A clear, current CV and relevant supporting information',1],
['mcq','What is Canva commonly used for?',['Creating visual designs and graphics','Repairing computer hardware','Managing Windows processes','Calculating Excel formulas'],'Creating visual designs and graphics',1],
['mcq','A trainee receives a message asking for their password through a suspicious link. What is the safest response?',['Do not provide the password and verify the request through an official channel','Enter the password quickly','Forward the password','Disable security software'],'Do not provide the password and verify the request through an official channel',1],
['mcq','Why is backing up important?',['It provides another copy of important data if the original is lost or damaged','It increases monitor resolution','It replaces antivirus software','It makes passwords unnecessary'],'It provides another copy of important data if the original is lost or damaged',1],
],
}
for mid, arr in qs.items():
    for typ,q,opts,ans,marks in arr:
        s=pdo.prepare("INSERT INTO questions(module_id,type,question,options_json,answer,marks) VALUES(?,?,?,?,?,?)")
        s.execute([mid,typ,q,json.dumps(opts),ans,marks])
}
}
echo '<!doctype html><html><head>'; include 'partials/head.php'; echo '</head><body><div class="login-wrap"><div class="card login-card">';
echo '<div class="brand">NYOTA ICT</div><h1>System ready</h1><p>The database, accounts, modules and starter question bank have been created.</p>';
echo '<p><strong>Admin:</strong> admin / admin123</p><p><strong>Trainees:</strong> alfred / alfred123, benson / benson123, pudens / pudens123, elizabeth / elizabeth123</p>';
echo '<p class="muted">For security, change these passwords before live use and delete or protect setup.php after installation.</p><a class="btn primary full" href="index.php">Open login</a></div></div></body></html>';