#!/bin/bash
/bin/bash -c "ssh -o StrictHostKeyChecking=no -i /key/process_maker.pem ubuntu@processmaker.fusionprofessionals.cloud 'sudo chmod -R 777 /opt/processmaker/'"
/bin/bash -c "scp -o StrictHostKeyChecking=no -r -i /key/process_maker.pem . ubuntu@processmaker.fusionprofessionals.cloud:/opt/processmaker/"
