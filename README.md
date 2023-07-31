# complaint system mysql table is
CREATE TABLE complaint (
    id INT AUTO_INCREMENT PRIMARY KEY,
    state VARCHAR(50) NOT NULL,
    district VARCHAR(50) NOT NULL,
    incident_description TEXT NOT NULL,
    individual_organization VARCHAR(100) NOT NULL,
    date DATE NOT NULL,
    time TIME,
    location VARCHAR(100) NOT NULL,
    additional_details TEXT,
    image_file VARCHAR(100),
    video_file VARCHAR(100),
    audio_file VARCHAR(100),
    document_file VARCHAR(100),
    timestamp DATETIME NOT NULL
);
