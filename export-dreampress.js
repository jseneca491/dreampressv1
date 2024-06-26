const fs = require('fs');
const path = require('path');
const AdmZip = require('adm-zip');
const mysql = require('mysql2');
const readline = require('readline');

const {
    exec
} = require('child_process');

// Get the current directory
const currentDirectory = process.cwd();
const dbbackupDirectory = 'C:/xampp/htdocs/';
const dbbackupFolderName = 'temp_backups';
const dbbackupFolderPath = path.join(dbbackupDirectory, dbbackupFolderName);
const baseFileName = 'dreampress-backup';
const backupDirectory = path.join(currentDirectory, 'wp-temp-backup');
const finalDirectory = path.join(currentDirectory, 'wp-migration-backup');
const files = fs.readdirSync(currentDirectory);
const currentDate = new Date();
const folderName = currentDate.toLocaleString('en-US', { timeZone: 'Asia/Singapore' });
const yellowColor = '\x1b[33m';
const greenColor = '\x1b[32m';
const purpleColor = '\x1b[35m';
const resetColor = '\x1b[0m';

const escapeRegExp = (string) => {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
};

const removeHttpHttps = (url) => {
    return url.replace(/^https?:\/\//, '');
};

// Create a backup directory
if (!fs.existsSync(backupDirectory)) {
    fs.mkdirSync(backupDirectory, {
        recursive: true
    });
}

if (!fs.existsSync(finalDirectory)) {
  fs.mkdirSync(finalDirectory, { recursive: true });
}

// Generate a folder name using the current date and time
const formattedDate = folderName.replace(/[/, :]/g, '-');
const newFolderName = `${formattedDate}`;

const newFolderPath = path.join(finalDirectory, newFolderName);
fs.mkdirSync(newFolderPath, {
  recursive: true
});

// Backup each file/directory in the current directory
console.log('Please wait as we create your backup.');
console.log('Creating backup...');

const copyFolderSync = (from, to) => {
    fs.mkdirSync(to, {
        recursive: true
    });
    fs.readdirSync(from).forEach(element => {
        if (fs.lstatSync(path.join(from, element)).isFile()) {
            fs.copyFileSync(path.join(from, element), path.join(to, element));
        } else {
            copyFolderSync(path.join(from, element), path.join(to, element));
        }
    });
};

files.forEach(file => {
    if (file !== 'wp-migration-backup' &&file !== 'wp-temp-backup' && file !== 'node_modules' && file !== '.git') {
        const sourcePath = path.join(currentDirectory, file);
        const targetPath = path.join(backupDirectory, file);
        if (fs.lstatSync(sourcePath).isDirectory()) {
            copyFolderSync(sourcePath, targetPath);
        } else {
            fs.copyFileSync(sourcePath, targetPath);
        }
    }
});

// Create a zip file
const zip = new AdmZip();
zip.addLocalFolder(backupDirectory);
zipFilePath = path.join(newFolderPath, `${baseFileName}.zip`);
zip.writeZip(zipFilePath);
console.log('Backup has been created!');
console.clear();
// Delete the backup folder
fs.rmSync(backupDirectory, {
    recursive: true
});

// Create readline interface for user input
const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout
});

// Prompt for existing and new domain names
rl.question(yellowColor+'Enter the existing domain name (http://domain-name.local): '+resetColor, existingDomain => {
    existingDomain = existingDomain.endsWith('/') ? existingDomain.slice(0, -1) : existingDomain;
    rl.question(yellowColor+'Enter the existing domain name (http://new-domain-name.local): '+resetColor, newDomain => {
        newDomain = newDomain.endsWith('/') ? newDomain.slice(0, -1) : newDomain;
        rl.question(yellowColor+'Enter the new file path (C:/xampp/htdocs/project_folder): '+resetColor, (suggestedFilePath) => {
            suggestedFilePath = suggestedFilePath.endsWith('/') ? suggestedFilePath.slice(0, -1) : suggestedFilePath;
            rl.close();

            // Retrieve database credentials
            const wpConfigPath = path.join(currentDirectory, 'wp-config.php');
            const wpConfigContent = fs.readFileSync(wpConfigPath, 'utf8');
            const dbNameMatch = wpConfigContent.match(/define\(\s*'DB_NAME',\s*'([^']+)'\s*\);/);
            const dbUserMatch = wpConfigContent.match(/define\(\s*'DB_USER',\s*'([^']+)'\s*\);/);
            const dbPasswordMatch = wpConfigContent.match(/define\(\s*'DB_PASSWORD',\s*'([^']+)'\s*\);/);
            const dbName = dbNameMatch ? dbNameMatch[1] : null;
            const dbUser = dbUserMatch ? dbUserMatch[1] : null;
            const dbPassword = dbPasswordMatch ? dbPasswordMatch[1] : '';

            // Connect to XAMPP MySQL
            const connection = mysql.createConnection({
                host: 'localhost',
                user: dbUser,
                password: dbPassword,
                database: dbName
            });

            // Export the database to SQL file
            if (!fs.existsSync(dbbackupFolderPath)) {
                // Create the "Database Backups" folder
                fs.mkdirSync(dbbackupFolderPath);
            }

            const exportFilePath = path.join(dbbackupFolderPath, `${dbName}.sql`);
            let exportCommand = `C:/xampp/mysql/bin/mysqldump -u ${dbUser}`;
            if (dbPassword) {
                exportCommand += ` -p${dbPassword}`;
            }
            exportCommand += ` ${dbName} > ${exportFilePath}`;
            console.log(`${greenColor}Status:${resetColor} `);
            console.log('Exporting the database...');

            // Execute the export command
            const exportProcess = exec(exportCommand, (error, stdout, stderr) => {
                if (error) {
                    console.error('Error exporting the database:', error);
                    connection.end();
                    return;
                }

                // Move the SQL file to currentDirectory
                const dbnewFilePath = path.join(newFolderPath, `${dbName}.sql`);
                fs.rename(exportFilePath, dbnewFilePath, (err) => {
                    if (err) {
                        console.error('Error moving the SQL file:', err);
                        return;
                    }

                    console.log('SQL file generated.');

                    // Read the exported SQL file
                    if (fs.existsSync(dbnewFilePath)) {
                        fs.readFile(dbnewFilePath, 'utf8', (error, data) => {
                            if (error) {
                                console.error('Error reading the exported SQL file:', error);
                                connection.end();
                                return;
                            }

                            // Replace the old domain name with the new domain name
                            const scriptPath = process.argv[1];
                            let filePath = path.dirname(scriptPath);
                            console.log(filePath);
                            filePath = filePath.replace(/\\/g, "/"); // normalize to forward slash
                            let recentDomainSQL = data.replace(new RegExp(escapeRegExp(removeHttpHttps(existingDomain)), 'g'), removeHttpHttps(newDomain));
                            let updatedDomainSQL = recentDomainSQL.replace(new RegExp(escapeRegExp(existingDomain), 'g'), newDomain);
                            let updatedSQL = updatedDomainSQL;
                            let newFilePath = suggestedFilePath.replace(/\\/g, '/');

                            const backwardSlashVariations = Array.from({ length: 4 }, (_, i) => 
                                filePath.replace(/\//g, "\\".repeat(i + 1))
                            );

                            const forwardSlashVariations = Array.from({ length: 4 }, (_, i) =>
                                filePath.replace(/\//g, "/".repeat(i + 1))
                            );

                            const allVariations = [...backwardSlashVariations, ...forwardSlashVariations];

                            for(let newvarFilePath of allVariations) {
                                updatedSQL = updatedSQL.replace(new RegExp(escapeRegExp(newvarFilePath), 'g'), newFilePath);
                            }

                            // Regex pattern to match serialized strings
                            const pattern = /s:(\d+):\\"([^\\"]*)\\"/g;

                            let correctedSQL = updatedSQL;

                            // Iterate through all serialized strings
                            let match;
                        
                            while ((match = pattern.exec(updatedSQL)) !== null) {
                                let fullMatch = match[0];
                                let oldLength = match[1];
                                let oldString = match[2];

                                // Replace old domain with new domain in the string
                                let newString = oldString.replace(new RegExp(escapeRegExp(existingDomain), 'g'), newDomain);

                                // Compute the new length of the string
                                let newLength = Buffer.byteLength(newString, 'utf8');

                                // Replace the old serialized string with the new serialized string in the SQL data
                                if (newLength !== Number(oldLength)) {
                                    let newSerializedString = 's:' + newLength + ':"' + newString + '"';
                                    correctedSQL = correctedSQL.replace(fullMatch, newSerializedString);
                                }
                            }

                            // Write the updated SQL to a new file
                            fs.writeFile(dbnewFilePath, correctedSQL, 'utf8', error => {
                                if (error) {
                                    console.error('Error writing the updated SQL file:', error);
                                    connection.end();
                                    return;
                                }

                                console.log('Database exported successfully!');

                                // Perform additional operations or finalize the script as needed
                                connection.end();

                                // Perform the rechecking after the success message
                                const count = (updatedSQL.match(new RegExp(existingDomain, 'g')) || []).length;
                                console.log(`Rechecking: ${count} occurrences of the domain '${existingDomain}' still exist.`);

                                const count2 = (updatedSQL.match(new RegExp(filePath, 'g')) || []).length;
                                console.log(`Rechecking: ${count2} occurrences of the domain '${filePath}' still exist.`);
                                
                                console.log('\n');
                                console.log(`${greenColor}Backup Directory:${resetColor} ${purpleColor}${zipFilePath}${resetColor}`);
                                console.log(`${greenColor}SQL Backup Directory:${resetColor} ${purpleColor}${dbnewFilePath}${resetColor}`);
                            });
                        });
                    }else{       
                        console.log(`${greenColor}Database was exported but not updated with the new domain!${resetColor}`);
                        connection.end();
                    }
                });
            });
        });
    });
});
