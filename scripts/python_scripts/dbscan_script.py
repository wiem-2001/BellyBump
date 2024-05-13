import sys
import json
from sklearn.cluster import DBSCAN
import numpy as np

def cluster_users_by_address(user_addresses):
    # Convert user addresses to numpy array
    encoded_addresses = np.arange(len(user_addresses)).reshape(-1, 1)
    
    # Define DBSCAN parameters
    eps = 0.1  # Epsilon neighborhood size
    min_samples = 2  # Minimum number of samples in a neighborhood
    
    # Initialize DBSCAN object
    dbscan = DBSCAN(eps=eps, min_samples=min_samples)
    
    # Perform clustering
    labels = dbscan.fit_predict(encoded_addresses)
    
    # Assign cluster labels to users
    clustered_users = {}
    for i, label in enumerate(labels):
        address = user_addresses[i]
        label_str = str(label)  # Convert label to string
        if label_str not in clustered_users:
            clustered_users[label_str] = {}
        if address not in clustered_users[label_str]:
            clustered_users[label_str][address] = 0
        clustered_users[label_str][address] += 1
    
    return json.dumps(clustered_users)

if __name__ == '__main__':
    # Read user addresses from command-line arguments
    user_addresses = json.loads(sys.argv[1])
    
    # Perform DBSCAN clustering
    clustered_users_json = cluster_users_by_address(user_addresses)
    
    # Print JSON string containing clustered users
    print(clustered_users_json)
