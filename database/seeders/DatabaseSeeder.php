<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\IncidentType;
use App\Models\Responder;
use App\Models\Incident;
use App\Models\Assignment;
use App\Models\Response;
use App\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with demo accounts and data for defense presentation.
     */
    public function run(): void
    {
        // Prevent duplicate seeding on redeploys if already seeded
        if (User::where('email', 'admin@spectrescue.test')->exists()) {
            return;
        }

        // 1. Seed Paranormal Incident Types
        $poltergeist = IncidentType::create([
            'name' => 'Poltergeist & Haunting',
            'description' => 'Unexplained physical disturbances, flying objects, cold spots, or knocking sounds in walls.',
            'default_threat_level' => 'High',
        ]);

        $possession = IncidentType::create([
            'name' => 'Demonic Possession',
            'description' => 'Hostile spiritual entity taking control of a living subject or causing levitation and glossolalia.',
            'default_threat_level' => 'Critical',
        ]);

        $apparition = IncidentType::create([
            'name' => 'Apparition / Ghost Attack',
            'description' => 'Visible apparition manifesting, emitting spectral residue, or physically intimidating occupants.',
            'default_threat_level' => 'High',
        ]);

        $cursedObject = IncidentType::create([
            'name' => 'Cursed Object / Relic',
            'description' => 'Antique furniture, dolls, mirrors, or occult artifacts emitting hazardous psychic energy.',
            'default_threat_level' => 'Medium',
        ]);

        $cryptid = IncidentType::create([
            'name' => 'Cryptid Sighting',
            'description' => 'Unclassified anomalous biological creature reported stalking residential or rural outskirts.',
            'default_threat_level' => 'Medium',
        ]);

        $shadowEntity = IncidentType::create([
            'name' => 'Shadow Entity',
            'description' => 'Dark shadowy silhouettes lurking in peripheral vision, causing battery drainage and extreme chills.',
            'default_threat_level' => 'Low',
        ]);

        // 2. Seed Users (All 4 Roles with password 'password')
        $admin = User::create([
            'name' => 'Chief Director Vance',
            'email' => 'admin@spectrescue.test',
            'phone' => '+63 917 888 7732',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $dispatcher = User::create([
            'name' => 'Officer Dana Scully',
            'email' => 'dispatcher@spectrescue.test',
            'phone' => '0917 555 0199',
            'role' => 'dispatcher',
            'password' => Hash::make('password'),
        ]);

        // Approved Responder 1 (Exorcist)
        $userResponder1 = User::create([
            'name' => 'Father John Ward',
            'email' => 'father.john@spectrescue.test',
            'phone' => '0918 555 0144',
            'role' => 'responder',
            'password' => Hash::make('password'),
        ]);

        $responder1 = Responder::create([
            'user_id' => $userResponder1->id,
            'specialization' => 'Senior Exorcist & Demonologist',
            'badge_number' => 'EXO-771',
            'bio' => '15 years experience in holy water rites, Roman rituals, and ancient demonic containment.',
            'verification_status' => 'approved',
            'is_available' => true,
            'verified_at' => now(),
        ]);

        // Approved Responder 2 (Ghost Hunter)
        $userResponder2 = User::create([
            'name' => 'Sarah Connor',
            'email' => 'sarah.connor@spectrescue.test',
            'phone' => '0920 555 0188',
            'role' => 'responder',
            'password' => Hash::make('password'),
        ]);

        $responder2 = Responder::create([
            'user_id' => $userResponder2->id,
            'specialization' => 'High-Tech Ghost Hunter',
            'badge_number' => 'GHT-204',
            'bio' => 'Equipped with calibrated EMF sensors, thermal imagers, and salt containment arrays.',
            'verification_status' => 'approved',
            'is_available' => true,
            'verified_at' => now(),
        ]);

        // Pending Responder (Medium)
        $userResponder3 = User::create([
            'name' => 'Edgar Allan Poe',
            'email' => 'edgar.poe@spectrescue.test',
            'phone' => '0922 555 0133',
            'role' => 'responder',
            'password' => Hash::make('password'),
        ]);

        $responder3 = Responder::create([
            'user_id' => $userResponder3->id,
            'specialization' => 'Clairvoyant Medium',
            'badge_number' => 'MED-013',
            'bio' => 'Skilled in seances, psychic readings, and spirit pacification. Awaiting agency verification.',
            'verification_status' => 'pending',
            'is_available' => false,
            'verified_at' => null,
        ]);

        // Citizens
        $citizen1 = User::create([
            'name' => 'Arthur Pendelton',
            'email' => 'citizen@spectrescue.test',
            'phone' => '0917 555 0111',
            'role' => 'citizen',
            'password' => Hash::make('password'),
        ]);

        $citizen2 = User::create([
            'name' => 'Evelyn Reed',
            'email' => 'citizen2@spectrescue.test',
            'phone' => '0919 555 0122',
            'role' => 'citizen',
            'password' => Hash::make('password'),
        ]);

        // 3. Seed Incidents with different statuses for Defense Demonstration

        // Incident 1: Resolved Incident
        $incident1 = Incident::create([
            'citizen_id' => $citizen1->id,
            'incident_type_id' => $poltergeist->id,
            'tracking_code' => 'PAR-2026-0001',
            'title' => 'Violent Kitchen Poltergeist and Flying Cutlery',
            'description' => 'Cabinet doors were banging non-stop, silverware flew across the room hitting the wall, and the room temperature plunged to sub-zero within minutes.',
            'location_address' => '13 Balete Drive, New Manila',
            'location_city' => 'Quezon City',
            'latitude' => 14.6231000,
            'longitude' => 121.0365000,
            'threat_level' => 'High',
            'status' => 'Resolved',
            'created_at' => now()->subDays(2),
        ]);

        $assignment1 = Assignment::create([
            'incident_id' => $incident1->id,
            'responder_id' => $responder2->id,
            'dispatcher_id' => $dispatcher->id,
            'status' => 'accepted',
            'dispatcher_notes' => 'Citizen is panicked. Deploy high-grade thermal sensors and salt barriers immediately.',
            'responder_notes' => 'On my way with EMF detector and iron traps.',
            'assigned_at' => now()->subDays(2)->addMinutes(10),
            'responded_at' => now()->subDays(2)->addMinutes(15),
        ]);

        Response::create([
            'incident_id' => $incident1->id,
            'responder_id' => $responder2->id,
            'containment_method' => 'EMF Surge Neutralization & Salt Ring Barrier',
            'findings_summary' => 'Identified Class IV Poltergeist attached to an antique 19th-century mahogany wall clock. Established an unbroken salt perimeter and ionized the air using EM emitters. The entity dematerialized completely.',
            'threat_neutralized' => true,
            'evidence_collected' => 'EMF recordings peaking at 78.4 mG; spectral ectoplasmic residue collected for lab testing.',
            'recommendations' => 'Keep the salt boundary active for 48 hours. Remove antique clock to containment storage.',
            'submitted_at' => now()->subDays(2)->addHours(3),
        ]);

        // Incident 2: Dispatched / Responding Incident
        $incident2 = Incident::create([
            'citizen_id' => $citizen2->id,
            'incident_type_id' => $possession->id,
            'tracking_code' => 'PAR-2026-0002',
            'title' => 'Unnatural Levitation and Aramaic Chanting in Attic',
            'description' => 'Victim is levitating two feet above the bed, speaking in archaic Aramaic dialect with dual-tone voice. Mirrors in the house cracked simultaneously.',
            'location_address' => 'Diplomat Hotel Road, Dominican Hill',
            'location_city' => 'Baguio City',
            'latitude' => 16.4024000,
            'longitude' => 120.5847000,
            'threat_level' => 'Critical',
            'status' => 'Responding',
            'created_at' => now()->subHours(4),
        ]);

        Assignment::create([
            'incident_id' => $incident2->id,
            'responder_id' => $responder1->id,
            'dispatcher_id' => $dispatcher->id,
            'status' => 'accepted',
            'dispatcher_notes' => 'CRITICAL THREAT: Priest and demonologist required urgently. Citizen has evacuated the second floor.',
            'responder_notes' => 'Ritual blessed relics and consecrated holy water packed. Arriving in 5 minutes.',
            'assigned_at' => now()->subHours(3)->addMinutes(30),
            'responded_at' => now()->subHours(3)->addMinutes(35),
        ]);

        // Incident 3: Pending Incident (Ready for Dispatcher to assign live in defense!)
        $incident3 = Incident::create([
            'citizen_id' => $citizen1->id,
            'incident_type_id' => $shadowEntity->id,
            'tracking_code' => 'PAR-2026-0003',
            'title' => 'Tall Shadow Figure Watching From Boiler Room',
            'description' => 'Every night around 3:00 AM, a 7-foot tall shadow silhouette manifests beside the boiler. It drains smartphone batteries instantly and leaves frosty footprints.',
            'location_address' => '88 Escolta Street, Binondo',
            'location_city' => 'City of Manila',
            'latitude' => 14.5995000,
            'longitude' => 120.9780000,
            'threat_level' => 'Medium',
            'status' => 'Pending',
            'created_at' => now()->subMinutes(25),
        ]);

        // 4. Sample In-App Notifications
        Notification::create([
            'user_id' => $dispatcher->id,
            'title' => 'New Emergency Incident Reported',
            'message' => 'Incident PAR-2026-0003 (Tall Shadow Figure) is awaiting dispatcher evaluation.',
            'link' => '/dispatcher/incidents/' . $incident3->id,
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $userResponder1->id,
            'title' => 'Emergency Dispatch Assignment',
            'message' => 'You were assigned to Critical Emergency PAR-2026-0002 at 13 Ravenwood Manor.',
            'link' => '/responder/dashboard',
            'is_read' => true,
        ]);

        Notification::create([
            'user_id' => $citizen1->id,
            'title' => 'Emergency Resolved',
            'message' => 'Your incident PAR-2026-0001 (Violent Kitchen Poltergeist) has been neutralized and closed.',
            'link' => '/citizen/incidents/' . $incident1->id,
            'is_read' => true,
        ]);
    }
}
